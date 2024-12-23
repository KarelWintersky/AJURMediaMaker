<?php

namespace AJURMediaMaker\Units;

class Convertor
{
    /**
     * @param string $filename
     * @param string $mimetype
     * @param array $processing_properties
     *
     * @return bool
     */
    public static function convert(string $filename, string $mimetype = '', array $processing_properties = []):bool
    {
        $src = config('path.storage.inbound');
        $dest = config('path.storage.outbound');

        return copy($src . DIRECTORY_SEPARATOR . $filename, $dest. DIRECTORY_SEPARATOR . $filename);
    }

}