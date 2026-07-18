$viewsPath = "c:\xampp\htdocs\Student_Housing_Management_System_PRD\shms\resources\views"
$files = Get-ChildItem -Path $viewsPath -Filter "*.blade.php" -Recurse
$count = 0

foreach ($file in $files) {
    $content = [System.IO.File]::ReadAllText($file.FullName, [System.Text.Encoding]::UTF8)
    $oldContent = $content
    $content = $content.Replace([char]0x0631 + [char]0x002E + [char]0x0633, [char]0x0631 + [char]0x002E + [char]0x064A)
    if ($content -ne $oldContent) {
        [System.IO.File]::WriteAllText($file.FullName, $content, [System.Text.Encoding]::UTF8)
        Write-Host "Updated: $($file.Name)"
        $count++
    }
}

Write-Host "`nDone! Updated $count files."
