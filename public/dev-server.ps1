$root = "C:\Users\b3108\Desktop\Fumomo-nuxt-master\php\public"
Add-Type -AssemblyName System.Net.HttpListener
$listener = New-Object System.Net.HttpListener
$listener.Prefixes.Add("http://localhost:8000/")
$listener.Start()
Write-Output "Serving $root at http://localhost:8000/"
while ($true) {
  $ctx = $listener.GetContext()
  $req = $ctx.Request
  $path = [System.Uri]::UnescapeDataString($req.Url.AbsolutePath)
  if ([string]::IsNullOrEmpty($path) -or $path -eq "/") { $rel = "test-nav.html" } else { $rel = $path.TrimStart("/") }
  $file = Join-Path $root $rel
  if (-not (Test-Path $file)) {
    $ctx.Response.StatusCode = 404
    $ctx.Response.OutputStream.Close()
    continue
  }
  $ext = [System.IO.Path]::GetExtension($file).ToLower()
  switch ($ext) {
    ".html" { $mime = "text/html" }
    ".css" { $mime = "text/css" }
    ".js" { $mime = "application/javascript" }
    ".svg" { $mime = "image/svg+xml" }
    ".ico" { $mime = "image/x-icon" }
    default { $mime = "application/octet-stream" }
  }
  $bytes = [System.IO.File]::ReadAllBytes($file)
  $ctx.Response.ContentType = $mime
  $ctx.Response.OutputStream.Write($bytes, 0, $bytes.Length)
  $ctx.Response.OutputStream.Close()
}