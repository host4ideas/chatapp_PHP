<?php
function checkAlias($alias)
{
    /*la w acepta letras, numeros y "_"*/
    $r = preg_match("/^[A-Za-z]\w{5,14}$/", $alias);
    if (!$r) return false;
    else return true;
}
function checkpassw($passw)
{
    if (strlen($passw) < 6 or strlen($passw) > 16) return FALSE;
    $mayu = preg_match("/[A-Z]/", $passw);
    $minu = preg_match("/[a-z]/", $passw);
    $nume = preg_match("/[0-9]/", $passw);
    $noalfa = preg_match("/[!-\\\\]/", $passw);
    return $minu and $mayu and $nume and $noalfa;
}

function uploadFile($file, $target_dir, $image)
{
    $errors = array();
    $evOK = 1;
    $fileExist = false;
    if ($image) {
        // Upload an image
        if ($file['size'] > 0) {
            $target_file = $target_dir . basename($file["name"]);

            if (is_uploaded_file($file['tmp_name'])) {
                $mime_type = mime_content_type($file['tmp_name']);

                $allowed_file_types = ['image/jpeg','image/png', 'image/gif', 'image/svg+xml', 'image/webp'];
                if (!in_array($mime_type, $allowed_file_types)) {
                    $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    $evOK = false;
                }
            }

            // Check if file already exists
            if (file_exists($target_file)) {
                $fileExist = true;
            }

            // Check file size
            if ($file["size"] > 500000) {
                $errors[] = "Sorry, your file is too large.";
                $evOK = false;
            }

            // Check if $evOK is set to 0 by an error
            if ($evOK == false) {
                $errors[] = "Sorry, your file was not uploaded.";
                return $errors;
                // if everything is ok, try to upload file
            } else if ($fileExist) {
                return $target_file;
                
            } else {
                $tryUpload = move_uploaded_file($file["tmp_name"], $target_file);
                if (!$tryUpload) {
                    $errors[] = "Sorry, there was an error uploading your file.";
                } else {
                    return $target_file;
                }
            }
        }
    } else {
        $target_file = $target_dir . basename($file["name"]);
        // Upload a document
        // Check file size
        if ($file["size"] > 500000) {
            $errors[] = "Sorry, your file is too large.";
            $evOK = false;
        }

        if (is_uploaded_file($file['tmp_name'])) {
            $mime_type = mime_content_type($file['tmp_name']);

            $allowed_file_types = ['application/pdf'];
            if (!in_array($mime_type, $allowed_file_types)) {
                $errors[] = "Sorry, only PDFs are allowed.";
                $evOK = false;
            }
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            $fileExist = true;
        }

        // Check if $evOK is set to 0 by an error
        if ($evOK == false) {
            $errors[] = "Sorry, your file was not uploaded.";
            return $errors;
            // if everything is ok, try to upload file
        } else if ($fileExist) {
            return $target_file;
        } else {
            $tryUpload = move_uploaded_file($file["tmp_name"], $target_file);
            if (!$tryUpload) {
                $errors[] = "Sorry, there was an error uploading your file.";
            } else {
                return $target_file;
            }
        }
    }
}
