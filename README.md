##实现功能：
* PHP对大整数的处理容易产生溢出， 把数字转换为字符串处理 <br>
* 采用拆分区间分块读数的方法 <br>

##使用方法： 
```
	$num = '2500100000903.123';
	echo $num.' >>> ';
	echo extMath::read($num);
```	