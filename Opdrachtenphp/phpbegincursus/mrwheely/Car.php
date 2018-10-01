<?php
class Car {
    var $name;
    var $image;
    var $price;
    
    function __construct($brand, $price, $image) {
        $this->brand = $brand;
        $this->price = $price;
        $this->image = $image;
    }
    
    function getImageName(){
        return $this->image;
    }
    
    function getBrand() {
        return $this->brand;
    }
    function getImage() {
        return $this->image;
    }
    function getPrice() {
        return $this->price;
    }
    function setBrand($brand) {
        $this->brand = $brand;
    }
    function setImage($image) {
        $this->image = $image;
    }
    function setPrice($price) {
        $this->price = $price;
    }
}