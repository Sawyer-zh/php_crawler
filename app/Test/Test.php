<?php

namespace Jrw\Test;

use Jrw\Crawler\TestCrawler;

class Test
{

    public $testCrawler;

    public $url = "https://tiku.baidu.com/web/quedetail/ebfc4c11f18583d0496459c9";

    public function __construct()
    {
        $this->testCrawler = new TestCrawler;
    }

    public function testDoRequest()
    {
        $element = [
            'url' => $this->url,
            'attempts' => 0,
        ];
        return $this->testCrawler->doRequest($element);
    }

    public function testSave2db($url, $res)
    {
        return $this->testCrawler->save2db($url, $res);
    }

    public function testGetLatest()
    {
        return $this->testCrawler->getLatest();
    }

}
