<?php
/*$file1 = $_FILES['file1']['error'];
$file2 = $_FILES['file2']['error'];
if ($file1 === 0) {
    $file = new SplFileObject($_FILES['file1']['tmp_name']);
    while (!$file->eof()) {
        var_dump($file->fgetcsv());
    }
}*/
class CSV
{
    private $file1 = null;
    private $file2 = null;
    /****
     * $fileCsv1 - шлях до першого файлу
     * $fileCsv2 - шлях до другого файлу
     */
    public function __construct(string $fileCsv1, string $fileCsv2)
    {
        if (file_exists($fileCsv1)) { //Якщо існує - записуєм у переміну
            $this->file1 = $fileCsv1;
        } else { //Якщо файла не має, визиваєм виключення
            throw new Exception("Файл " . $fileCsv1 . " не найден");
        }
        if (file_exists($fileCsv2)) { //Якщо існує - записуєм у переміну
            $this->file2 = $fileCsv2;
        } else { //Якщо файла не має, визиваєм виключення
            throw new Exception("Файл " . $fileCsv2 . " не найден");
        }
    }
    /*****
     * Запис у файл масиву з першого файлу
     */
    public function setCSVFile(array $fileGet)
    {
        $handle = fopen($this->file2, "a+"); //Відкриваєм файл для запису
        //fputcsv($handle,  $fileGet, ",");
        foreach ($fileGet as $value) { //Проходим массив і записуєм у файл

            fputcsv($handle,  $value, ",");
        }
        fclose($handle); //Закриваєм

    }
    //Робимо масив з csv
    public function getCSVFile()
    {
        $handle = fopen($this->file1, "r"); //відкриваєм для читання

        $array_line_full = array(); //Масив для зберігання данних
        //проходимо файл і читаємо по рядку
        while (($line = fgetcsv($handle, 0, ",")) !== FALSE) {
            $array_line_full[] = $line; //Записуєм в масив
        }
        fclose($handle);
        return $array_line_full;
    }
}
//Записуєм файли у папку

$file1 = $_FILES['file1']['tmp_name'];
$file2 = $_FILES['file2']['tmp_name'];

$uploaddir = '../upload/';
$uploadfile = $uploaddir . basename($_FILES['file1']['name']);
$uploadfile2 = $uploaddir . basename($_FILES['file2']['name']);
//Якщо файл існує видаляєм
if (file_exists($uploadfile)) {
    unlink($uploadfile);
}
if (file_exists($uploadfile2)) {
    unlink($uploadfile2);
}
move_uploaded_file($_FILES['file1']['tmp_name'], $uploadfile);
move_uploaded_file($_FILES['file2']['tmp_name'], $uploadfile2);
try {
    $csv = new CSV($uploadfile, $uploadfile2);
    /**
     * Формуєм масив з першого файлу
     */
    $arr = [];
    $get_csv = $csv->getCSVFile();
    //взнаємо ключ масива з необхідними даними
    $key = array_search('ul. Twarda 18', $get_csv);
    //var_dump($get_csv);
    $i = 0;
    foreach ($get_csv as $value => $row) { //Проходим по строкам
        $i++;
        $arr[] =  ['', '', '', '', '', '', '', '', '', '', '', $row[$key], '', '', ''];
    }
    //Взнаємо кількість рядків для log
    $date = Date('d-m-Y H:i:s');
    $log = fopen('../log/log.txt', "a+");
    fwrite($log, $date . ' Рядків записано - ' . $i . ', ');
    fclose($log);
    /**
     * Записуєм данні в другий файл
     */
    $csv->setCSVFile($arr);
} catch (Exception $e) { //Якщо файлу не має виводимо помилку
    echo "Помилка: " . $e->getMessage();
}
header("location: ../index.php");
