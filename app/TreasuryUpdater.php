<?php
/**
 * Created by PhpStorm.
 * User: robin
 * Date: 21/05/18
 * Time: 15:35
 */

namespace App;

interface TreasuryUpdater
{
    public function getId(): int;


    public function getValue(): float;


    public function getRelativeValue(): float;
}