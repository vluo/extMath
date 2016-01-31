<?php

header("Content-type:text/html;charset=utf-8");


/*
 * 读一个非负整数或小数
 * PHP对大整数的处理容易产生溢出， 把数字转换为字符串处理
 * 采用拆分区间分块读数的方法
 * @author vluo
 * */
class extMath
{
    public static function read($num)
    {
        $numbers = explode('.', $num);
        $float = isset($numbers[1]) ? $numbers[1] : 0;
        $int = $numbers[0];
        $len = strlen($int);
        //位数不足，补全，保证位数是4的整数倍，为分组做准备
        if ($len < 4) {
            $int = str_pad($int, 4, "0", STR_PAD_LEFT);
            $len = 4;
        } elseif ($len > 4 && $len < 8) {
            $int = str_pad($int, 8, "0", STR_PAD_LEFT);
            $len = 8;
        }

        $fragNums = array();
        $flags = ceil($len / 4);
        $limit = 4;
        for ($i = 1; $i <= $flags; $i++) {
            if (count($fragNums) == 2) {//亿以上数字 一次读取
                $start = 0;
                $limit = $len - 8;
            } else {
                $start = -4 * $i;
            }
            $fragNums[] = strval(sprintf('%04d', substr($int, $start, $limit)));
            if(count($fragNums) == 3) {
                break;
            }
        }

        $fragCount = count($fragNums);
        $readString = array();
        //单位
        $units = array('', '万', '亿');
        $units = array_slice($units, 0, $fragCount);
        for ($i = $fragCount - 1; $i >= 0; $i--) {
            $fragNum = $fragNums[$i];
            if (strlen($fragNum) > 4) {//亿以上数字如果超过4位，递归处理
                $readString[] = self::read($fragNum).$units[$i];
            } else {
                $readResult = self::_readNum($fragNum);
                //读零的特殊处理
                if (isset($fragNums[$i + 1]) && $fragNum[0] == 0 && end($readString) != '零') {
                    $readString[] = '零';// . $readResult;
                }
                if (!empty($readResult)) {
                    $readString[] = $readResult.$units[$i];
                    //$readString[] = ;
                }
            }
        }

        if ($float) {
            $readString[] = '点' . self::_readNum($float, TRUE);
        }
        return implode('', $readString);
    }

    private static function _readNum($int, $float = FALSE)
    {
        $int = strval($int);
        $len = strlen($int);
        $readStr = array();
        $units = array('千', '百', '十', '');
        $i=0;
        for ($i; $i < $len; $i++) {
            $num = intval($int[$i]);
            //0前缀不读，连续零读一个， 个位0不读
            if ($num == 0 && !$float && (empty($readStr) || in_array('零', $readStr) || $i==($len-1))) {
                continue;
            }
            $readStr[] = self::_num2word($num) . (($num == 0 || $float) ? '' : $units[$i]);
        }
        //var_dump($readStr);
        if(end($readStr) == '零') {
            array_pop($readStr);
        }
        return implode('', $readStr);
    }

    private static function _num2word($num)
    {
        if ($num < 0 || $num > 9) {
            return '';
        }
        $words = array('零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖');
        return $words[$num];
    }
}
$num = '2500100000903.123';
echo $num.' >>> ';
echo extMath::read($num);

?>



	