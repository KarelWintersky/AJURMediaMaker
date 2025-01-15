```php
<?php

if ( ! function_exists ( 'mime_content_type ' ) )
{
   function mime_content_type ( $f )
   {
       return trim ( exec ('file -bi ' . escapeshellarg ( $f ) ) ) ;
   }
}

?>
```
