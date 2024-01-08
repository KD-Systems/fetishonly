<?php

namespace App\Jobs;

use App\Model\Attachment;
use App\Model\Post;
use App\Providers\AttachmentServiceProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use FFMpeg\Filters\Video\CustomFilter;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Video\X264;
use Intervention\Image\Facades\Image;

class VideoPostingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $post, $attachments;

    public static $videoEncodingPresets = [
        'size' => ['videoBitrate'=> 500, 'audioBitrate' => 128],
        'balanced' => ['videoBitrate'=> 1000, 'audioBitrate' => 256],
        'quality' => ['videoBitrate'=> 2000, 'audioBitrate' => 512],
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($post, $attachments)
    {
        $this->post = Post::with('user')->find($post->id);
        $this->attachments = $attachments;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        $storage = Storage::disk(config('filesystems.defaultFilesystemDriver'));
        $storageDriver = config('filesystems.defaultFilesystemDriver');
        $directory = 'videos';
        foreach($this->attachments as $attachment) {
            $fileId = $attachment->id;
            $fileContent = file_get_contents($attachment->path);
            // Move tmp file onto local files path, as ffmpeg can't handle absolute paths
            $filePath = $attachment->id.'.'.$attachment->type;
            Storage::disk('tmp')->put($filePath, $fileContent);

            $fileExtension = 'mp4';
            $newfilePath = $directory.'/'.$attachment->id.'.'.$fileExtension;

            // Converting the video
            $video = FFMpeg::
            fromDisk('tmp')
                ->open($filePath);

            logger(getSetting('media.apply_watermark'));

            logger("CHECK 1");
            // Add watermark if enabled in admin
            if (getSetting('media.apply_watermark')) {
                logger("CHECK 2");
                $dimensions = $video
                    ->getVideoStream()
                    ->getDimensions();

                logger(getSetting('media.watermark_image'));
                if(getSetting('media.watermark_image')) {
                    logger("CHECK 3");
                    // Add watermark to post images
                    $watermark = Image::make(self::getWatermarkPath());
                    $tmpWatermarkFile = 'watermark-' . $fileId . '-.png';
                    $resizePercentage = 75; //70% less then an actual image (play with this value)
                    $watermarkSize = round($dimensions->getWidth() * ((100 - $resizePercentage) / 100), 2); //watermark will be $resizePercentage less then the actual width of the image
                    // resize watermark width keep height auto
                    $watermark->resize($watermarkSize, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $watermark->encode('png', 100);
                    Storage::disk('tmp')->put($tmpWatermarkFile, $watermark);
                    if (getSetting('media.apply_watermark')) {
                        $video->addWatermark(function (WatermarkFactory $watermark) use ($fileId, $tmpWatermarkFile) {
                            $watermark->fromDisk('tmp')
                                ->open($tmpWatermarkFile)
                                ->right(25)
                                ->bottom(25);
                        });
                    }
                }

                logger(getSetting('media.use_url_watermark'));
                if(getSetting('media.use_url_watermark')){
                    logger("CHECK 4");
                    $textWaterMark = str_replace(['https://','http://','www.'],'',route('profile',['username'=>$this->post->user->username]));
                    $textWaterMarkSize = 3 / 100 * $dimensions->getWidth();
                    // Note: Some hosts might need to default font on public_path('/fonts/OpenSans-Semibold.ttf') instead of verdana
                    $filter = new CustomFilter("drawtext=text='".$textWaterMark."':x=10:y=H-th-10:fontfile='".(env('FFMPEG_FONT_PATH') ?? 'Verdana')."':fontsize={$textWaterMarkSize}:fontcolor=white: x=(w-text_w)-25: y=(h-text_h)-35");
                    $video->addFilter($filter);
                }

            }

            // Re-converting mp4 only if enforced by the admin setting
            if($attachment->type == 'mp4' && getSetting('media.enforce_mp4_conversion')){
                $filePath = $directory.'/'.$attachment->id.'.'.$fileExtension;
                $storage->put($filePath, $fileContent, 'public');
            }
            else{
                // Overriding default ffmpeg lib temporary_files_root behaviour
                $ffmpegOutputLogDir = storage_path() . '/logs/ffmpeg';
                $ffmpegPassFile = $ffmpegOutputLogDir . '/' . uniqid();
                if(!is_dir($ffmpegOutputLogDir)){
                    mkdir($ffmpegOutputLogDir);
                }

                $videoQualityPreset = self::$videoEncodingPresets[getSetting('media.ffmpeg_video_conversion_quality_preset')];
                $video = $video->export()
                    ->toDisk(config('filesystems.defaultFilesystemDriver'));
                if(getSetting('media.ffmpeg_audio_encoder') == 'aac'){
                    $video->inFormat((new X264('aac', 'libx264'))->setKiloBitrate($videoQualityPreset['videoBitrate'])->setAudioKiloBitrate($videoQualityPreset['audioBitrate']));
                }
                elseif(getSetting('media.ffmpeg_audio_encoder') == 'libmp3lame'){
                    $video->inFormat((new X264('libmp3lame'))->setKiloBitrate($videoQualityPreset['videoBitrate'])->setAudioKiloBitrate($videoQualityPreset['audioBitrate']));
                }
                elseif (getSetting('media.ffmpeg_audio_encoder') == 'libfdk_aac'){
                    $video->inFormat((new X264('libfdk_aac', 'libx264'))->setKiloBitrate($videoQualityPreset['videoBitrate'])->setAudioKiloBitrate($videoQualityPreset['audioBitrate']));
                }
                $video->addFilter('-preset', 'ultrafast')
                    #->addFilter(['-strict', 2])
                    ->addFilter(['-passlogfile', $ffmpegPassFile])
                    ->save($newfilePath);

                if(file_exists($ffmpegPassFile.'-0.log')) unlink($ffmpegPassFile.'-0.log');
                if(file_exists($ffmpegPassFile.'-1.log')) unlink($ffmpegPassFile.'-1.log');

            }

            Storage::disk('tmp')->delete($filePath);
            if (getSetting('media.apply_watermark') && getSetting('media.watermark_image')) {
                Storage::disk('tmp')->delete($tmpWatermarkFile);
            }
            $filePath = $newfilePath;

            Attachment::where('id', $attachment->id)->update([
                'filename' => $filePath,
                'type' => $fileExtension,
                'driver' => AttachmentServiceProvider::getStorageProviderID($storageDriver),
            ]);
        }

        Post::where('id', $this->post->id)->update([
            'status' => Post::APPROVED_STATUS
        ]);

    }


    public static function getWatermarkPath()
    {
        $watermark_image = getSetting('media.watermark_image');
        if($watermark_image){
            if (strpos($watermark_image, 'download_link')) {
                $watermark_image = json_decode($watermark_image);
                if ($watermark_image) {
                    $watermark_image = Storage::disk(config('filesystems.defaultFilesystemDriver'))->path($watermark_image[0]->download_link);
                }
            }
        }
        else{
            $watermark_image = public_path('img/logo-black.png');
        }
        return $watermark_image;
    }
}
