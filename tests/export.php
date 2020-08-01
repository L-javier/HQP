<?php

   function export_excel(array $fieldsName, array $fields, array $data, $title)
    {
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=$title.xlsx");
        header("Pragma: no-cache");
        header("Expires: 0");
        $objPHPExcel = new \PHPExcel();
        $sheet = $objPHPExcel->setActiveSheetIndex(0);
        try {
            if (empty($fields)) {
                throw new \Exception("字段不能为空");
            }
            if (empty($fieldsName)) {
                throw new \Exception("字段别名不能为空");
            } else {
                $col = 'A';
                foreach ($fieldsName as $val) {
                    $sheet->setCellValue(($col++) . '1', $val);
                }
                unset($str);
            }
            $row = 2;
            if (empty($data)) {
                throw new \Exception("数据内容不能为空");
            } else {
                foreach ($data as $value) {
                    $col = 'A';
                    /*foreach ($value as $k => $v) {
                        if (in_array($k, $fields)) {
                            $sheet->setCellValueExplicit(($col++) . $row, isset($v) ? $v : '-', \PHPExcel_Cell_DataType::TYPE_STRING);
                        }
                    }*/

                    foreach ($fields as $k => $v) {
                        $sheet->setCellValueExplicit(($col++) . $row, isset($value[$v]) ? $value[$v] : '-', \PHPExcel_Cell_DataType::TYPE_STRING);
                    }
                    $row++;
                }
            }
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        } catch (\Exception $e) {
            echo $e->getFile() . PHP_EOL . $e->getLine() . PHP_EOL . $e->getCode() . PHP_EOL . $e->getMessage();
            exit;
        }
    }
    
    
  function export_csv($headlist = array(), $fields = array(), $data = array(), $title)
    {

        //header('Content-Type: application/vnd.ms-excel');

        header('Content-Encoding: UTF-8');
        header("Content-Type: text/csv; charset=UTF-8");

        header('Content-Disposition: attachment;filename="' . $title . '.csv"');
        header('Cache-Control: max-age=0');

        //打开PHP文件句柄,php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');

        //输出Excel列名信息
        foreach ($headlist as $key => $value) {
            //CSV的Excel支持GBK编码，一定要转换，否则乱码
            $headlist[$key] = iconv('utf-8', 'gbk', $value);
        }

        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $headlist);

        //计数器
        $num = 0;

        //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 100000;

        //逐行取出数据，不浪费内存
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {

            $num++;

            //刷新一下输出buffer，防止由于数据过多造成问题
            if ($limit == $num) {
                ob_flush();
                flush();
                $num = 0;
            }

            $rows = $data[$i];

            /* foreach ($rows as $k => $value) {
                 if(in_array($k,$fields)){
                     $row[$k] = iconv('utf-8', 'gbk', $value);
                 }
             }*/
            foreach ($fields as $k => $v) {
                $row[$v] = iconv('utf-8', 'gbk', $rows[$v]);
                if ($v == 'order_id') $row[$v] = $rows[$v] . "\t";
            }

            fputcsv($fp, $row);

        }

    }

