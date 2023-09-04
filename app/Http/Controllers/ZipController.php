<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;

use ZipArchive;
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;
use phpseclib\Net\SCP;

class ZipController extends Controller
{
    public function zipSatuDirektori()
    {
        $zipFileName = 'satuFile.zip';
        $directoryToZip = public_path('tes');
        $password = 'password';

        if (is_dir($directoryToZip)) {
            // Membuat objek ZipArchive
            $zip = new ZipArchive();

            // Membuka file ZIP dengan kata sandi
            if ($zip->open(public_path($zipFileName), ZipArchive::CREATE) === TRUE) {
                // Daftar semua file di dalam direktori "tes" dan tambahkan ke dalam zip
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directoryToZip));
                foreach ($files as $name => $file) {
                    // Pastikan itu adalah file (bukan direktori)
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        // Tentukan nama relatif file dalam zip
                        $relativeNameInZipFile = substr($filePath, strlen($directoryToZip) + 1);

                        // Menggunakan pustaka phpseclib untuk mengenkripsi ZIP
                        $zip->setEncryptionName($relativeNameInZipFile, ZipArchive::EM_AES_256);
                        $zip->setPassword($password);

                        // Menambahkan file ke dalam ZIP
                        $zip->addFile($filePath, $relativeNameInZipFile);
                    }
                }

                // Menutup ZIP
                $zip->close();

                // Mengirimkan file ZIP dengan kata sandi
                return response()->download(public_path($zipFileName));
            } else {
                return response()->json(['error' => 'Gagal membuka zip file.']);
            }
        } else {
            return response()->json(['error' => 'Direktori "tes" tidak ditemukan.']);
        }
    }

    public function zipSatuFile()
    {

        $fileName = 'satuFile.zip';
        $filePath = public_path('gambar.jpg');
        $password = 'password';

        if (File::exists($filePath)) {
            // Membuat objek ZipArchive
            $zip = new ZipArchive();

            // Membuka file ZIP dengan kata sandi
            if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE) {
                // Mendapatkan nama file dari path
                $relativeNameInZipFile = basename($filePath);

                // Menggunakan pustaka phpseclib untuk mengenkripsi ZIP
                $zip->setEncryptionName($relativeNameInZipFile, ZipArchive::EM_AES_256);
                $zip->setPassword($password);

                // Menambahkan file ke dalam ZIP
                $zip->addFile($filePath, $relativeNameInZipFile);

                // Menutup ZIP
                $zip->close();

                // Mengirimkan file ZIP dengan kata sandi
                return response()->download(public_path($fileName));
            } else {
                return response()->json(['error' => 'Gagal membuka zip file.']);
            }
        } else {
            return response()->json(['error' => 'File gambar.jpg tidak ditemukan.']);
        }
    }
}
