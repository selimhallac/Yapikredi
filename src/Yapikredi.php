<?php

namespace Phpdev;

Class Yapikredi
{
    
    
    public $username = "";
    public $pasword = "";
    public $customerno = "";
    
    function __construct($username, $password, $customerno)
    {
        $this->username       = $username;
        $this->password       = $password;
        $this->customerno     = $customerno;
    }
    
    
    public function hesap_hareketleri($tarih1, $tarih2)
    {
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://dpextprd.yapikredi.com.tr/Hmn/EhoAccountTransactionService",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => '<soapenv:Envelope xmlns:intf="http://intf.service.electronicaccountsummary.eho.hmn.ykb.com/" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
        <soapenv:Header>
            <wsse:Security soapenv:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" 
            xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                <wsse:UsernameToken wsu:Id="UsernameToken-D1A5C91F8C11FC7F2614479411111111">
                    <wsse:Username>'.$data['username'].'</wsse:Username>
                    <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.$data['password'].'</wsse:Password>
                    <wsse:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">
                    GQ5DlhNjctYjRlMC00MmNjLWI5YjYtOWZhNWFiOWNhYjg2</wsse:Nonce>
                    <wsu:Created>2017-01-12T14:07:00.620Z</wsu:Created>
                </wsse:UsernameToken>
            </wsse:Security>
        </soapenv:Header>
        <soapenv:Body>
            <intf:sorgula>
                <!--Optional:-->
                <arg0>
                    <!--Optional:-->
                    <baslangicSaat>0000</baslangicSaat>
                    <!--Optional:-->
                    <baslangicTarih>'.date('Ymd',strtotime($data['start_date'])).'</baslangicTarih>
                    <!--Optional:-->
                    <bitisSaat>2359</bitisSaat>
                    <!--Optional:-->
                    <bitisTarih>'.date('Ymd',strtotime($data['end_date'])).'</bitisTarih>
                    <!--Optional:-->
                    <dovizKodu></dovizKodu>
                    <!--Optional:-->
                    <firmaKodu>'.$data['subeno'].'</firmaKodu>
                    <!--Optional:-->
                    <hesapNo></hesapNo>
                </arg0>
            </intf:sorgula>
        </soapenv:Body>
        </soapenv:Envelope>'
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        if (!$err) {
            $dom = new DOMDocument();
            $dom->loadXML($response);
            $soapBody = $dom->getElementsByTagName('Body')->item(0);
            $bodyContent = $soapBody->C14N();
            $bodyContent = preg_replace('/(<\/?|\s)ns2:/', '$1', $bodyContent);
            // XML'i SimpleXMLElement nesnesine dönüştürün
            $bodyXml = simplexml_load_string($bodyContent, "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($bodyXml);
            $array = json_decode($json);
        }  
        if(isset($array->sorgulaResponse->return)){
            return json_encode([
                'statu'=>true,
                'response' => $array->sorgulaResponse->return
            ]);
        } else {
            return json_encode([
                'statu'=>false,
                'response' => 'Bir hata oluştu, bilgileri kontrol ediniz.'
            ]);
        }
    }
    
    
}