<?php

namespace App;

use JsonSerializable;

class Product implements JsonSerializable
{
    
    private string $title;
    private float $price;
    private string $imageURL;
    private int $capacityMB;
    private string $colour;
    private string $avabilityText;
    private bool $isAvailable;
    private string $shippingText;
    private string $shippingDate;
    
    public function getTitle(string $title): void
    {
        $this->title = $title;
    }
    
    public function getPrice(float $price): void
    {
        $this->price = $price;
    }
    
    public function getImageURL(string $imageURL): void
    {
        $this->imageURL = $imageURL;
    }
    
    public function getCapacityMB(int $capacityMB): void
    {
        $this->capacityMB = $capacityMB;
    }
    
    public function getColour(string $colour): void
    {
        $this->colour = $colour;
    }
    
    public function getAvabilityText(string $avabilityText): void
    {
        $this->avabilityText = $avabilityText;
    }
    
    public function getAvability(bool $isAvailable): void
    {
        $this->isAvailable = $isAvailable;
    }
    
    public function getShippingText(string $shippingText): void
    {
        $this->shippingText = $shippingText;
    }
    
    public function getShippingDate(string $shippingDate): void
    {
        $this->shippingDate = $shippingDate;
    }
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

}
