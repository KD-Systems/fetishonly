<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadAttachamentRequest;
use App\Providers\AttachmentServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class FileUploadController extends Controller
{

    public function uploadFile(Request $request) {

        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            // return $this->saveFile($save->getFile());
            $saveRequest = new UploadAttachamentRequest(['file'=>$save->getFile()]);
            $saveRequest->validate($saveRequest->rules());
            $saveRequest->merge(['type' => 'post']);
            return $this->upload($saveRequest, 'post', $save->getFile());
        }

        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

    /**
     * Saves the file to S3 server
     *
     * @param UploadedFile $file
     *
     * @return JsonResponse
     */
    protected function saveFile(UploadedFile $file)
    {
        $fileName = $this->createFilename($file);
        // Group files by mime type
        $mime = str_replace('/', '-', $file->getMimeType());
        // Group files by the date (week
        $dateFolder = date("Y-m-W");

        // Build the file path
        $filePath = "upload/{$mime}/{$dateFolder}/";
        $finalPath = storage_path("app/".$filePath);

        // move the file name
        $file->move($finalPath, $fileName);

        return response()->json([
            'path' => $filePath,
            'name' => $fileName,
            'mime_type' => $mime
        ]);
    }

     /**
     * Create unique filename for uploaded file
     * @param UploadedFile $file
     * @return string
     */
    protected function createFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = str_replace(".".$extension, "", $file->getClientOriginalName()); // Filename without extension

        // Add timestamp hash to name of the file
        $filename .= "_" . md5(time()) . "." . $extension;

        return $filename;
    }


    protected function upload(UploadAttachamentRequest $request, $type = false, $chunkedFile = false)
    {

        if($chunkedFile){
            $file = $chunkedFile;
        }
        else{
            $file = $request->file('file');
        }

        $type = $request->route('type');

        $fileMimeType = $file->getMimeType();
        try {
            switch ($fileMimeType) {
                case 'video/mp4':
                case 'video/avi':
                case 'video/quicktime':
                case 'video/x-m4v':
                case 'video/mpeg':
                case 'video/wmw':
                case 'video/x-matroska':
                case 'video/x-ms-asf':
                case 'video/x-ms-wmv':
                case 'video/x-ms-wmx':
                case 'video/x-ms-wvx':
                    $directory = 'videos';
                    break;
                case 'audio/mpeg':
                case 'audio/ogg':
                case 'audio/wav':
                    $directory = 'audio';
                    break;
                case 'application/vnd.ms-excel':
                case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                case 'application/pdf':
                    $directory = 'documents';
                    break;
                default:
                    $directory = 'images';
                    break;
            }

            $generateThumbnail = false;
            if ($type == 'post') {
                $directory = 'posts/'.$directory;
                $generateThumbnail = true;
            } elseif ($type == 'message') {
                $directory = 'messenger/'.$directory;
                $generateThumbnail = true;
            } elseif ($type == 'payment-request'){
                $directory = 'payment-request/'.$directory;
            }

            $attachment = AttachmentServiceProvider::createAttachment($file, $directory, $generateThumbnail);

            if($chunkedFile){
                unlink($file->getPathname());
            }

        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => [$exception->getMessage()]], 500);
        }

        return response()->json([
            'success' => true,
            'attachmentID' => $attachment->id,
            'path' => Storage::url($attachment->filename),
            'type' => AttachmentServiceProvider::getAttachmentType($attachment->type),
            'thumbnail' => AttachmentServiceProvider::getThumbnailPathForAttachmentByResolution($attachment, 150, 150),
        ]);
    }
}
