<?php

namespace AJURMediaMaker\Units;

use Arris\Entity\Result;

class OutgoingFile
{
    public Result $state;

    /**
     * @var string
     */
    public string $uuid;

    /**
     * @var array
     */
    public array $fileinfo;

    /**
     * @var string
     */
    public string $filepath;

    /**
     * @var string
     */
    public string $fast_filepath;

    public function __construct($file, $uuid, $extension = 'jpg')
    {
        $this->state = new Result();

        $this->uuid = $uuid;
        $this->filepath = config('path.storage.outbound') . DIRECTORY_SEPARATOR . $file;
        $this->fileinfo = [
            'fn_public' =>  $uuid . '.' . $extension,
            'mimetype'  =>  mime_content_type($this->filepath),
            'filesize'  =>  filesize($this->filepath)
        ];
    }

}