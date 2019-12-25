<?php
/**
 * Created by PhpStorm.
 * User: v_bivwei
 * Date: 2019/12/23
 * Time: 14:32
 */

namespace Xiaobinqt\BiaoQingBao;
require_once "simple_html_dom.php";


use GuzzleHttp\Client;

class BiaoQingBao
{
    public $keyWords = "";
    protected $guzzleOptions = array();
    private $url = "";
    protected $suffix = array(
        "png",
        "jpg",
        "jpeg",
        "gif"
    );
    protected $num = null; // 获取几条数据

    /**
     * BiaoQingBao constructor.
     * @param $keyWords
     */
    public function __construct($keyWords, $guzzleOptions = array())
    {
        $this->keyWords = $keyWords;
        $this->guzzleOptions = $guzzleOptions;
        $this->url = "https://www.doutula.com/search?type=photo&more=1&keyword=";
    }


    /**
     * @param array $suffix
     * @description
     * @author v_bivwei
     */
    public function setSuffix(array $suffix)
    {
        $this->suffix = $suffix;
    }


    /**
     * @return array
     * @description
     * @author v_bivwei
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @param $num
     * @description
     * @author v_bivwei
     */
    public function setNeedCount($num)
    {
        $this->num = $num;
    }

    /**
     * @return null
     * @description
     * @author v_bivwei
     */
    public function getNeedCount()
    {
        return $this->num;
    }

    public function setGuzzleOptions(array $guzzleOptions)
    {
        $this->guzzleOptions = $guzzleOptions;
    }


    public function getGuzzleOptions()
    {
        return $this->guzzleOptions;
    }

    /**
     * @return Client
     * @description
     * @author v_bivwei
     */
    protected function getHttpClient()
    {
        return new Client();
    }


    /**
     * @return int|null
     * @description
     * @author v_bivwei
     */
    private function MaxNum()
    {
        if (is_null($this->num) || $this->num <= 0) {
            return PHP_INT_MAX;
        }
        return $this->num;
    }


    /**
     * @return \simple_html_dom
     * @description
     * @author v_bivwei
     */
    protected function getSimpleHtmlDom()
    {
        return new \simple_html_dom();
    }

    public function getEmojiList()
    {
        $html = $this->getSimpleHtmlDom();
        if (empty($this->keyWords)) {
            return $this->returnInfo(0, "success", array());
        }
        try {
            $client = $this->getHttpClient();
            $url = $this->url . $this->keyWords . "&page=1";
            $response = $client->get($url, $this->guzzleOptions)->getBody()->getContents();
            $html->load($response);
            $urlArr = array();
            $pageNum = array();

            /**
             * @var @input \simple_html_dom_node[]
             */
            $input = $html->find('a[class=col-xs-6 col-md-2]');

            /**
             * @var $value \simple_html_dom_node
             */
            foreach ($input as $key => $value) {
                /**
                 * @var $childImg \simple_html_dom_node[]
                 */
                $childImg = $value->find("img");
                foreach ($childImg as $child) {
                    $imgUrl = $child->getAttribute("data-original");
                    if ($imgUrl) {
                        $suffix = $this->getFileSuffix($imgUrl);
                        if (!in_array($suffix, $this->getSuffix())) {
                            continue;
                        }
                        array_push($urlArr, $imgUrl);
                        if ($this->whetherReachMax($urlArr)) {
                            $html->clear();
                            return $this->returnInfo(0, "success", $urlArr);
                        }
                    }
                }
            }

            // 分页
            $linkNode = $html->find("a[class='page-link']");
            if (!empty($linkNode)) {
                /**
                 * @var $link \simple_html_dom_node
                 */
                foreach ($linkNode as $link) {
                    $pageText = $link->text();
                    if (is_numeric($pageText)) {
                        array_push($pageNum, $pageText);
                    }
                }

                $max = max($pageNum); // 最后一页
                for ($i = 2; $i <= $max; $i++) {
                    $url = $this->url . $this->keyWords . "&page=" . $i;
                    $this->getSourceByUrl($url, $urlArr);
                    if ($this->whetherReachMax($urlArr)) {
                        return $this->returnInfo(0, "success", $urlArr);
                    }
                }

            }

            return $this->returnInfo(0, "success", $urlArr);
        } catch (\Exception $exception) {
            return $this->returnInfo(-1, $exception->getMessage());
        }

    }


    /**
     * @param $url
     * @param $urlArr
     * @description
     * @author v_bivwei
     */
    protected function getSourceByUrl($url, &$urlArr)
    {
        $html = $this->getSimpleHtmlDom();
        $rs = $this->getHttpClient()->request('GET', $url, $this->guzzleOptions)
            ->getBody()
            ->getContents();
        $html->load($rs);

        /**
         * @var @input simple_html_dom_node[]
         */
        $input = $html->find('a[class=col-xs-6 col-md-2]');
        /**
         * @var $value \simple_html_dom
         */
        foreach ($input as $key => $value) {
            /**
             * @var $childImg \simple_html_dom_node[]
             */
            $childImg = $value->find("img");
            foreach ($childImg as $child) {
                $imgUrl = $child->getAttribute("data-original");
                if ($imgUrl) {
                    $suffix = $this->getFileSuffix($imgUrl);
                    if (!in_array($suffix, $this->getSuffix())) {
                        continue;
                    }
                    array_push($urlArr, $imgUrl);
                }
            }
        }
        $html->clear();
    }


    /**
     * @param int $error
     * @param string $msg
     * @param array $data
     * @return array
     * @description
     * @author v_bivwei
     */
    protected function returnInfo($error = 0, $msg = "success", $data = array())
    {
        return json_encode(
            array(
                "error" => $error,
                "msg"   => $msg,
                "data"  => $data
            )
        );
    }


    /**
     * @param $array
     * @return bool
     * @description 是否到达设置的最大值
     * @author v_bivwei
     */
    private function whetherReachMax($array)
    {
        return count($array) >= $this->MaxNum();
    }


    /**
     * @param $fileName
     * @return false|string
     * @description 获取文件后缀名
     * @author v_bivwei
     */
    private function getFileSuffix($fileName)
    {
        return strtolower(substr($fileName, strrpos($fileName, '.') + 1));
    }


}