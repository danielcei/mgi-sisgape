<?php
//$fileContents = Illuminate\Support\Facades\Storage::disk('ftp')->get($getState());
//Storage::disk('public')->put('shalom/'.$getState(), $fileContents);
$url = Storage::disk('public')->url('shalom/'.$getState());
?>
<img src="{{ $url }}" alt="Imagem" style="width: 300px;margin:1%" >
