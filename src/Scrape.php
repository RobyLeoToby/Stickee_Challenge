<?php

namespace App;

use Symfony\Component\DomCrawler\Crawler;
use Exception;
use Symfony\Component\DomCrawler\UriResolver;

require 'vendor/autoload.php';

class Scrape
{
    private array $products = [];

    public function run(): void
    {
        $document = ScrapeHelper::fetchDocument('https://www.magpiehq.com/developer-challenge/smartphones');
        
        $pages = $document->filter("#pages")->filter("a");
        $products = $document->filter(".product");
        $pageCount = count($pages);
        $this->getProductDetailes($products);
        
        for ($page = 2; $page <= $pageCount; $page++)
        {
            $document = ScrapeHelper::fetchDocument('https://www.magpiehq.com/developer-challenge/smartphones/?page='.$page);
            $products = $document->filter(".product");
            $this->getProductDetailes($products);
        }
        
        file_put_contents('output.json', json_encode($this->products));
    }
    
    private function getProductDetailes(Crawler $products): void
    {
        foreach ($products as $product)
        {
            try {
                $crawler = new Crawler($product);
                
                
                
                $colourOptions = $crawler->filter("span[data-colour]")->each(function (Crawler $node, $i)
                {
                    return $node->attr("data-colour");
                });
                
                $phoneModel = $crawler->filter(".product-name")->text();
                
                $capacity = $crawler->filter(".product-capacity")->text();
                
                $title = $phoneModel." ".$capacity;
                
                $capacityGB = (int) filter_var($capacity, FILTER_SANITIZE_NUMBER_INT);
                
                if (strstr($capacity, "GB") !== false)
                {
                    $capacityMB = $capacityGB *1000;
                }
                else 
                {
                    $capacityMB = $capacityGB;
                }
                
                $priceString = $crawler->filterXPath("//div[contains(text(), '£')]")->text();
                $price = (float) filter_var($priceString, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                
                $imageSource = $crawler->filter("img")->attr("src");
                $imageURL = UriResolver::resolve($imageSource, "https://www.magpiehq.com/developer-challenge/smartphones");
                
                $availabilityText = $crawler->filterXPath("//div[contains(text(), 'Availability:')]")->text();
                $availabilityText = trim(substr($availabilityText,strrpos($availabilityText,':') + 1));
                
                if ($availabilityText == "Out of Stock")
                {
                    $isAvailable = false;
                    //$shippingText = null;
                    //$shippingDate = null;
                }
                else 
                {
                    $isAvailable = true;
                }
                
              
                
                foreach ($colourOptions as $colour) 
                {
                    $phone = new Product();
                    
                    $phone->getTitle($title);
                    $phone->getCapacityMB($capacityMB);
                    $phone->getColour($colour);
                    $phone->getPrice($price);
                    $phone->getImageURL($imageURL);
                    $phone->getAvability($isAvailable);
                    //$phone->getShippingText($shippingText);
                    //$phone->getShippingDate($shippingDate);

                    if(!in_array($phone, $this->products))
                    {
                        $this->products[] = $phone;
                    }
                    
                }
                
                
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}

$scrape = new Scrape();
$scrape->run();
