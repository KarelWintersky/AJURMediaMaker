<?php

namespace AJURMediaMaker\Units;

class OutgoingFile
{
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

    public function __construct($uuid, $extension = 'jpg')
    {
        $this->uuid = $uuid;
        $this->filepath = config('path.storage.outbound') . '/' . $uuid . '.' . $extension;
        $this->fileinfo = [
            'fn_public' =>  $uuid . '.' . $extension,
            'mimetype'  =>  mime_content_type($this->filepath),
            'filesize'  =>  filesize($this->filepath)
        ];
    }

}