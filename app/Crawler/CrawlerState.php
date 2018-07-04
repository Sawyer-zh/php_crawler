<?php

namespace Jrw\Crawler;

interface CrawlerState{

    function getFailed();

    function getFinished();

    function getQueued();

    function getSaved();

}