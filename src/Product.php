<?php

namespace App;

use Symfony\Component\DomCrawler\Crawler;

class Product
{
    public static function getProducts($document){
        $products = [];
       
        if ($document) {
            foreach ($document as $key => $currentDocument) {
                    //getting data and put them in array
                    $currentDocument->filter("div.flex-wrap div.product")->each(function(Crawler $node, $index) use(&$products,&$getCorrentColour){
                        $products[] = [
                        'title' => $node->filter("span.product-name")->text(""),
                        'price' => str_replace("£","", $node->filter("div.text-lg")->text("")),
                        'image' => str_replace("../",'',"https://www.magpiehq.com/developer-challenge/".$node->filter("img")->attr('src')),
                        'capacityMB' => Product::getCapacityMB($node->filter("span.product-capacity")->text("")),
                        'colour' => Product::getColour($node->filter("div.px-2 span.rounded-full")),
                        'availabilityText'=> $availabilityText = str_replace("Availability:","", $node->filter("div.text-sm")->text("")),
                        'isAvailable'=> ( str_contains($availabilityText, "Out of Stock") ) ? "false" : "true" ,
                        'shippingText'=> ($node->filter("div.text-sm")->last()->text("") == "Availability: Out of Stock") ? ""  : $node->filter("div.text-sm")->last()->text(""),
                        'shippingDate'=> Product::getDate($node->filter("div.text-sm")->last()->text("")),
                        
                    ];
                });
                
            }
        }
        $products = Product::duplicateProductByColour($products);
        return  array_values(array_unique($products,SORT_REGULAR)); //returing product in array, removing duplicats of array, and resetting array keys
    }

    //function for getting capacity of phone in mbs
    private static function getCapacityMB(string $capacity){
        $totalCapacityMB = "";
        if (substr($capacity, -2) == "GB") { //Checking if capacity is in GB
            $getGB = (int)filter_var($capacity, FILTER_SANITIZE_NUMBER_INT);//getting numerical value from string given
            $totalCapacityMB = 8000 * $getGB;
        }else{
            $totalCapacityMB = (int)filter_var($capacity, FILTER_SANITIZE_NUMBER_INT); //getting numerical value from string given
        }

        return $totalCapacityMB; //returing capacity of phone
    }
    //end of function: getting capacity of phone in mbs

    //function to get colours of product
    private static function getColour( $currentNode){
     
            $colour = [];
            //creating array of colours
            $currentNode->each(function(Crawler $node, $index) use(&$colour){
                $colour[] = $node->filter("span.rounded-full")->attr("data-colour");
                
            });
            //end of creating array of colours

           // $colour = str_replace(array("[", '"',"]"), "",json_encode($colour)); //converting array to string
            return $colour ;
    
    }
    //end of function to get colours of product


    //function to date of shipping product
    public static function getDate(string $shippingDate){
        $date = "";
        if (($pos = strpos($shippingDate, "Delivers")) !== FALSE) { 
            $date = substr($shippingDate, $pos+9); 
        }
        if (($pos = strpos($shippingDate, "Delivery")) !== FALSE) { 
            $date = substr($shippingDate, $pos+8); 
        }
        if (($pos = strpos($shippingDate, "have it")) !== FALSE) { 
            $date = substr($shippingDate, $pos+8); 
        }
        
        if (($pos = strpos($shippingDate, "Available on")) !== FALSE) { 
            $date = substr($shippingDate, $pos+13); 
        }
        return $date;
    }
    //end of function to get date of shipping product

    //function that make Each colour variant to be treated as a separate product.
    private static function duplicateProductByColour($products){
        $new_products = [];

        if ($products) {
            foreach ($products as $key => $products_value) {
                if($products_value['colour']){
                    foreach ($products_value['colour'] as $new_products_value) {
                        $new_products[] = [
                            'title' => $products_value['title'],
                            'price' => $products_value['price'],
                            'image' => $products_value['image'],
                            'capacityMB' => $products_value['capacityMB'],
                            'colour' => $new_products_value,
                            'availabilityText'=> $products_value['availabilityText'],
                            'isAvailable'=> $products_value['isAvailable'],
                            'shippingText'=> $products_value['shippingText'],
                            'shippingDate'=> $products_value['shippingDate'],
                        ];
                    }
                }
            }
        }
        print_r($new_products);
        return $new_products;
    }
   // end of function that make Each colour variant to be treated as a separate product.//
}
