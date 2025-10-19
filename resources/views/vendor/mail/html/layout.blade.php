<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
@media only screen and (max-width: 600px) {
.inner-body {
width: 100% !important;
}

.footer {
width: 100% !important;
}
}

@media only screen and (max-width: 500px) {
.button {
width: 100% !important;
}
}
</style>
</head>
<body>
    <style>BODY{
        font-family:Tahoma;
        line-height:30px;
        font-size:20px;
        margin:0px;
        padding:0px;
    }.content{
        padding:5px !important;
        margin:5px !important;
    }.button-primary{
        background-color:#222;
        padding:0px;
        border-radius:6px;
        border:2px solid #ffc600;
        line-height:20px;
        font-size:20px;
        color:#ffc600;
        font-family:Tahoma;
        margin:3px;
        text-decoration:none;
    }.button-primary:hover,a.button-primary:visited:hover{
    background-color:#ffc600 !important;
    color:#222 !important;
    border:3px solid #ffc600 !important;
    padding:5px 7px !important;
    }</style>
    <div style="background-color:#000;background-position:right;height:52px;background-repeat:no-repeat;background-image:url('https://www.marblefx.de/_images/mailheader/mcsl_grad.png');">
        <div style="float:left;position:relative;margin-top:0px;margin-left:0px;">
        <img src="https://www.marblefx.de/_images/mailheader/mcsl_mail_system2.png" alt="MCS Mail System" title="MCS Mail System">
    </div><br>
<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center">
<table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
{{ $header ?? '' }}

<!-- Email Body -->
<tr>
<td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;">
<table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<!-- Body content -->
<tr>
<td class="content-cell">
{{ Illuminate\Mail\Markdown::parse($slot) }}

{{ $subcopy ?? '' }}
</td>
</tr>
</table>
</td>
</tr>

{{ $footer ?? '' }}
</table>
</td>
</tr>
</table>
</body>
</html>
