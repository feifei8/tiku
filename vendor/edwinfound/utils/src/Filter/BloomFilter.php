<?php

namespace EdwinFound\Utils\Filter;


class BloomFilter implements Filter
{
    const NAME = 'bloomFilter';

    // bit set length
    private $m;
    // number of strings to hash
    private $n;
    // number of hashing functions
    private $k;
    // hashing block with size m

    /**
     * BloomFilter constructor.
     * @param $maxN : max number
     * @param $m : block size
     */
    function __construct($maxN, $m)
    {
        $this->m = $m;
        $this->n = $maxN;
        $this->k = ceil(($this->m / $this->n) * log(2));
        $this->bitset = array_fill(0, ceil($this->m / 32), 0);
    }

    private $bitset = null;

    public static function build($maxN, $m = null)
    {
        if ($m === null) {
            // 依据 http://pages.cs.wisc.edu/~cao/papers/summary-cache/node8.html
            // 哈希函数个数k取10，位数组大小m设为字符串个数n的20倍时
            // false positive发生的概率是0.0000889
            $m = $maxN * 20;
        }
        return new BloomFilter($maxN, $m);
    }

    private function hashCodes($str)
    {
        $res = array();
        $seed = crc32($str);
        // set random seed, or mt_rand
        // wouldn't provide same random arrays
        // at different generation
        mt_srand($seed);
        for ($i = 0; $i < $this->k; $i++) {
            $res[] = mt_rand(0, $this->m - 1);
        }
        return $res;
    }

    public function save($file)
    {
        $f = fopen($file, 'w+');
        foreach ($this->bitset as $value) {
            echo $value . ' ';
            fwrite($f, pack('i', $value));
        }
        fclose($f);
    }

    public function restore($file)
    {
        $f = fopen($file, 'r');
        foreach ($this->bitset as $index => $value) {
            $d = fread($f, 4);
            $d = unpack('i', $d);
            $this->bitset[$index] = $d[1];
        }
        fclose($f);
    }

    public function add($key)
    {
        $hashes = $this->hashCodes($key);
        foreach ($hashes as $codeBit) {
            $offset = intval($codeBit / 32);
            $bit = $codeBit % 32;
            $this->bitset[$offset] |= (1 << $bit);
        }
    }

    public function has($key)
    {
        $hashes = $this->hashCodes($key);
        foreach ($hashes as $codeBit) {
            $offset = intval($codeBit / 32);
            $bit = $codeBit % 32;
            if (!($this->bitset[$offset] & (1 << $bit))) {
                return false;
            }
        }
        return true;
    }
}