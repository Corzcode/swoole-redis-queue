<?php

/*
 * PHP version 5.6
 */
namespace Core;

/**
 * 打包解包
 * 
 * @author  Corz<combo_k@126.com>
 * @version 1.0
 */
class Packet
{

    /**
     * 打包长度
     * @var int
     */
    const HEADER_SIZE = 4;

    /**
     * 打包标设
     * @var string
     */
    const HEADER_STRUCT = "Nlength";

    /**
     * 打包标设
     * @var string
     */
    const HEADER_PACK = "N";

    /**
     * 打包数据
     * @param string $data
     * @param int    $serid
     * @return string
     */
    public static function encode($data)
    {
        return pack(self::HEADER_PACK, strlen($data)) . $data;
    }

    /**
     * 解析头部
     *
     * @param  string $data
     * @return array
     */
    public static function decode(&$data)
    {
        $header = substr($data, 0, self::HEADER_SIZE);
        $data = substr($data, self::HEADER_SIZE);
        return $header ? unpack(self::HEADER_STRUCT, $header) : '';
    }
}