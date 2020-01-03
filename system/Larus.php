<?php
namespace Yilmaz;

class Larus
{

    public function getApi($api,$queryString){
        if(is_callable([$this,$api])){
            return call_user_func([$this,$api],$queryString);
        }else{
            return $this->json(404,'Called method not found');
        }
    }

    public function weather($parameter){
        $url = "http://www.haber7.com/api/widget/weather/".$parameter;
        $getUrl = file_get_contents($url);
        return $this->json(200,json_decode($getUrl));
    }

    public function namaz($parameter){
        $url = "http://www.haber7.com/api/widget/pray-times/".$parameter."?format=json";
        $getUrl = file_get_contents($url);
        return $this->json(200,json_decode($getUrl));
    }

    public function doviz($parameter = NULL){
        if(isset($parameter)){
            $url = "https://www.tcmb.gov.tr/kurlar/today.xml";
            $getUrl = file_get_contents($url);
            $xml = simplexml_load_string($getUrl);
            foreach($xml->Currency as $currency){
                if($currency->attributes()->Kod == $parameter){
                    return $this->json(200,['data' => $currency]);
                }else{
                    return $this->json(303, 'Gönderdiğiniz Currency Kod Bulunamadı');
                }
            }
        }else{
            $url = "https://finans.truncgil.com/today.json";
            $getUrl = file_get_contents($url);
            return $this->json(200,['data' => json_decode($getUrl)]);
        }
    }
    public function yakit($parameter){
        if(isset($parameter)){
            $url = "https://www.tppd.com.tr/tr/akaryakit-fiyatlari?id=".$parameter;
            $getUrl = file_get_contents($url);
            preg_match_all('@<tr>(.*?)</tr>@si',$getUrl,$match);
            $data = array();
            $newData = [
                'İlçe' => '',
                'KURŞUNSUZ BENZİN (TL/LT)' => '',
                'GAZ YAĞI (TL/LT)' => '',
                'TP MOTORİN (TL/LT)' => '',
                'MOTORİN (TL/LT)' => '',
                'KALORİFER YAKITI (TL/KG)' => '',
                'FUEL OIL (TL/KG)' => '',
                'Y.K. FUEL OIL (TL/KG)' => '',
                'TPGAZ' => '',
            ];
            $lastArray = [];
            foreach(array_shift($match) as $matches){
                preg_match_all('@<td data-title="(.*?)">(.*?)</td>@si',$matches,$new);
                array_push($data,$new);
            }
            foreach($data as $items){
                if(count($items[2]) > 0) {
                    $newData["İlçe"] = @trim($items[2][0]);
                    $newData["KURŞUNSUZ BENZİN (TL/LT)"] = @trim($items[2][1]);
                    $newData["GAZ YAĞI (TL/LT)"] = @trim($items[2][2]);
                    $newData["TP MOTORİN (TL/LT)"] = @trim($items[2][3]);
                    $newData["MOTORİN (TL/LT)"] = @trim($items[2][4]);
                    $newData["KALORİFER YAKITI (TL/KG)"] = @trim($items[2][5]);
                    $newData["FUEL OIL (TL/KG)"] = @trim($items[2][6]);
                    $newData["Y.K. FUEL OIL (TL/KG)"] = @trim($items[2][7]);
                    $newData["TPGAZ"] = @trim($items[2][8]);
                    array_push($lastArray,$newData);
                }
            }
            return $this->json(200,['message' => 'Başarılı', 'data' => $lastArray]);
        }else{
            return $this->json(303,'Lütfen il plaka kodu gönderiniz');
        }
    }

    public function json($statusCode, $message){
        header_remove();
        http_response_code($statusCode);
        header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
        header('Content-Type: application/json');
        $status = array(
            200 => '200 OK',
            201 => '201 Created',
            202 => '202 Accepted',
            204 => '204 No Content',
            301 => '301 Moved Permanently',
            302 => '302 Found redirect',
            303 => '303 Everything ok but not response',
            400 => '400 Bad Request',
            401 => '401 Unauthorized',
            403 => '403 Forbidden',
            404 => '404 Not Found',
            405 => '405 Method Not Allowed',
            415 => '415 Unsupported Media Type',
            422 => 'Unprocessable Entity',
            500 => '500 Internal Server Error',
            501 => '501 Not Implemented'
        );
        header('Status: '.$status[$statusCode]);
        return json_encode(array(
            'status' => $statusCode < 300,
            'message' => $message
        ));

    }
}