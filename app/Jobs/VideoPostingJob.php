<?php

namespace App\Jobs;

use App\Model\Attachment;
use App\Model\Post;
use App\Providers\AttachmentServiceProvider;
use App\Providers\NotificationServiceProvider;
use App\User;
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
use Ramsey\Uuid\Uuid;

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
            // Move tmp file onto local files path, as ffmpeg can't handle absolute paths
            $filePath = $attachment->id.'.'.$attachment->type;
            // Storage::disk('tmp')->put($filePath, $fileContent);

            $fileExtension = 'mp4';
            $fileId = Uuid::uuid4()->getHex();
            $newfilePath = $directory.'/'.$fileId.'.'.$fileExtension;

            $textWaterMark = str_replace(['https://','http://','www.'],'',route('profile',['username'=>$this->post->user->username]));


            $video = FFMpeg::openUrl($attachment->path)
                ->export()
                // ->toDisk($storageDriver)
                ->inFormat(new \FFMpeg\Format\Video\X264);

            $dimensions = $video
                ->getVideoStream()
                ->getDimensions();

            $textWaterMarkSize = 3 / 100 * $dimensions->getWidth();
            // Note: Some hosts might need to default font on public_path('/fonts/OpenSans-Semibold.ttf') instead of verdana
            $filter = new CustomFilter("drawtext=text='".$textWaterMark."':x=10:y=H-th-10:fontfile='".(public_path('fonts/OpenSans-Regular.ttf'))."':fontsize={$textWaterMarkSize}:fontcolor=white: x=(w-text_w)-25: y=(h-text_h)-35");
            $video->addFilter($filter);

            $video->save($newfilePath);

            AttachmentServiceProvider::removeAttachment($attachment);

            Attachment::where('id', $attachment->id)->update([
                'filename' => $newfilePath,
                'type' => $fileExtension,
                'driver' => AttachmentServiceProvider::getStorageProviderID($storageDriver),
            ]);
        }

        Post::where('id', $this->post->id)->update([
            'status' => Post::APPROVED_STATUS
        ]);


        try {
            NotificationServiceProvider::createVideoPublishNotification($this->post);
        } catch (\Throwable $th) {
            //throw $th;
        }

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
