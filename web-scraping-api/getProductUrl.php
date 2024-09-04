<?php
header('Content-Type: text/html; charset=utf-8');
 class getProductUrl{
    public  $data=[];
    public $producUrlArray=[];
    public $productDatas=[];
    

  public $myCurl;
  public $maxCount=4;
  public $url;
 

  function __construct()
  {
     $this->myCurl= curl_init();
     $this->url="https://steamcommunity.com/market/search/render/?query=&start=10&count="."$this->maxCount"."&search_descriptions=0&sort_column=popular&sort_dir=desc";
  }
  // return first url html data
  function getBaseData()
  {
    echo json_encode(array(
        "status"=>"1",
         "mesage"=> " base url: ".$this->url
    ));
    curl_setopt_array($this->myCurl,
         [
             CURLOPT_URL=>$this->url,
             CURLOPT_RETURNTRANSFER=>true,
             CURLOPT_TIMEOUT=>60,
             CURLOPT_HTTPHEADER => array(
              'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
              'Accept: application/json, text/javascript, */*; q=0.01',
              'X-Requested-With: XMLHttpRequest'
          ),
             
         ]
  );
  
  
    
    $result= curl_exec($this->myCurl);
    $data= json_decode($result,true);
    if ($data !== null && isset($data['results_html'])) {
     
        return $data['results_html'];
    
  } else {
    echo json_encode(array(
        "status"=>"0",
         "mesage"=> "Veri çekilemedi."
    ));
      
  }
  }
    

    function getProductsUrl()
    {
       
   
    
         $firstHtml= $this->getBaseData();
      if (isset($firstHtml)) {
       preg_match_all('/href="([^"]*)"/', $firstHtml, $productUrl);
       
    } else {
        echo json_encode(array(
            "status"=>"0",
             "mesage"=> "Veri çekilemedi."
        ));
    }
    
       
    
    
      curl_close($this->myCurl);
      if($productUrl!= null)
      {
    
        echo json_encode(array(
            "status"=>"1",
            "mesage"=>" urller bulundu"
        ));
        $this->productDatas= $productUrl[1];
   
        return $productUrl[1];
        }
      else
      {
        echo json_encode(array(
          "status"=>"0",
          "mesage"=>" result is null"
      ));
      } 
     
    }
    function getProductsId()
    {
        print_r($this->getBaseData());
        $firstHtml= $this->getBaseData();
      if (isset($firstHtml)) {
       
       preg_match_all('/Market_LoadOrderSpread\(\s*(\d+)\s*\)/', $firstHtml,$amountProductId);
    } else {
        echo json_encode(array(
            "status"=>"0",
             "mesage"=> "Veri çekilemedi."
        ));
    }
     
       
    
    
      curl_close($this->myCurl);
      if($amountProductId!= null)
      {
        return $amountProductId[1];
      }
      else
      {
        echo json_encode(array(
          "status"=>"0",
          "mesage"=>" result is null"
      ));
      } 
     
    }
    function createProductsUrl()
    {
        $productsId= $this->getProductsId();
        $productsIdCount=count($productsId) ;
        if($productsIdCount>0)
        {
            echo json_encode(array(
            "status"=>"1",
            "mesage"=>" products id Count: ".$productsIdCount
            ));
            
            for ($i=0; $i < $productsIdCount ; $i++) { 
                $urls[$i]= "https://steamcommunity.com/market/itemordershistogram?country=TR&language=turkish&currency=1&item_nameid=".$productsId;
             }
             return $urls;
        }
        else
        {
            echo json_encode(array(
                "status"=>"0",
                "mesage"=>" products id Count: ".$productsIdCount
                ));
                return null;
        }
        

    }
   
 }
