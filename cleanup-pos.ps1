# Cleanup script for POS file corruption
$filePath = "d:\FSQTAR-PROJECTS\salon-cms\salon-cms-master\resources\views\pos\index.blade.php"

# Read file line by line to preserve structure
$lines = Get-Content -Path $filePath

$cleanedLines = foreach ($line in $lines) {
    # Fix split template expressions: $ {variable} -> ${variable}
    $line = $line -replace '\$\s+\{', '${'
    
    # Fix split closing braces with excessive spaces: }     -> }
    $line = $line -replace '\}\s{5,}', '} '
    
    # Fix HTML attributes with spaces: class = "..." -> class="..."
    $line = $line -replace '(class|style|id|colspan|src|alt|href|data-[\w-]+|type|value|name|placeholder|readonly|disabled|checked|selected)\s*=\s*', '$1='
    
    # Fix opening tags with spaces: < div -> <div
    $line = $line -replace '<\s+(\w)', '<$1'
    
    # Fix closing tags with spaces: </ div -> </div
    $line = $line -replace '</\s+(\w)', '</$1'
    
    # Fix excessive whitespace between non-whitespace chars (but preserve indentation)
    # Only fix if there are 20+ spaces between visible characters
    $line = $line -replace '(\S)\s{20,}(\S)', '$1 $2'
    
    # Output the cleaned line
    $line
}

# Write back to file
$cleanedLines | Set-Content -Path $filePath -Encoding UTF8

Write-Host "Cleanup complete. File has been processed."
