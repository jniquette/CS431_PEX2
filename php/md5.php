<?php 
// //Note: All variables are unsigned 32 bit and wrap modulo 2^32 when calculating
// var int[64] s, K
// No need to predefine variables in php.

// //s specifies the per-round shift amounts
$shiftAmounts = array(	7, 12, 17, 22,  7, 12, 17, 22,  7, 12, 17, 22,  7, 12, 17, 22,
			5,  9, 14, 20,  5,  9, 14, 20,  5,  9, 14, 20,  5,  9, 14, 20,
			4, 11, 16, 23,  4, 11, 16, 23,  4, 11, 16, 23,  4, 11, 16, 23,
			6, 10, 15, 21,  6, 10, 15, 21,  6, 10, 15, 21,  6, 10, 15, 21);

//Load values of K into an array using K[i] := floor(abs(sin(i + 1)) × (2 pow 32))

// //Use binary integer part of the sines of integers (Radians) as constants:
// for i from 0 to 63
    // K[i] := floor(abs(sin(i + 1)) × (2 pow 32))
// end for
// //(Or just use the following table):
$K = array(	hexdec("d76aa478"), hexdec("e8c7b756"), hexdec("242070db"), hexdec("c1bdceee"),
		hexdec("f57c0faf"), hexdec("4787c62a"), hexdec("a8304613"), hexdec("fd469501"),
		hexdec("698098d8"), hexdec("8b44f7af"), hexdec("ffff5bb1"), hexdec("895cd7be"),
		hexdec("6b901122"), hexdec("fd987193"), hexdec("a679438e"), hexdec("49b40821"),
		hexdec("f61e2562"), hexdec("c040b340"), hexdec("265e5a51"), hexdec("e9b6c7aa"),
		hexdec("d62f105d"), hexdec("02441453"), hexdec("d8a1e681"), hexdec("e7d3fbc8"),
		hexdec("21e1cde6"), hexdec("c33707d6"), hexdec("f4d50d87"), hexdec("455a14ed"),
		hexdec("a9e3e905"), hexdec("fcefa3f8"), hexdec("676f02d9"), hexdec("8d2a4c8a"),
		hexdec("fffa3942"), hexdec("8771f681"), hexdec("6d9d6122"), hexdec("fde5380c"),
		hexdec("a4beea44"), hexdec("4bdecfa9"), hexdec("f6bb4b60"), hexdec("bebfbc70"),
		hexdec("289b7ec6"), hexdec("eaa127fa"), hexdec("d4ef3085"), hexdec("04881d05"),
		hexdec("d9d4d039"), hexdec("e6db99e5"), hexdec("1fa27cf8"), hexdec("c4ac5665"),
		hexdec("f4292244"), hexdec("432aff97"), hexdec("ab9423a7"), hexdec("fc93a039"),
		hexdec("655b59c3"), hexdec("8f0ccc92"), hexdec("ffeff47d"), hexdec("85845dd1"),
		hexdec("6fa87e4f"), hexdec("fe2ce6e0"), hexdec("a3014314"), hexdec("4e0811a1"),
		hexdec("f7537e82"), hexdec("bd3af235"), hexdec("2ad7d2bb"), hexdec("eb86d391"));

//Initialize variables:
$a0 = hexdec("67452301");   //A
$b0 = hexdec("efcdab89");   //B
$c0 = hexdec("98badcfe");   //C
$d0 = hexdec("10325476");   //D

//Get input from user
//Source: http://stackoverflow.com/questions/6543841/php-cli-getting-input-from-user-and-then-dumping-into-variable-possible
// $handle = fopen ("php://stdin","r");
// $input = trim(fgets($handle));
//Skip this for now, just use the string
$input = "The quick brown fox jumps over the lazy dog";
//$input = "abcdefghijklmnopqrstuvwxyz";

echo "Inputted: ".$input."\n";
echo bin2hex($input)."\n\n";	//The bin2hex() function converts a string of ASCII characters to hexadecimal values.

//Convert to byte array: http://stackoverflow.com/questions/885597/string-to-byte-array-in-php
$byte_array = unpack('C*', $input);
$originalSize = sizeof($byte_array)*8;

// //Pre-processing: adding a single 1 bit
// append "1" bit to message    
// /* Notice: the input bytes are considered as bits strings,
  // where the first bit is the most significant bit of the byte.[47]
$byte_array[] = (int) 128;//This gives us 0b10000000

// //Pre-processing: padding with zeros
// append "0" bit until message length in bits ≡ 448 (mod 512)
// this is equal to length in bytes %64 = 56
while(sizeof($byte_array)%64 != 56){
	$byte_array[] = (int) 0;
} 

// append original length in bits (in hex) mod (2 pow 64) to message (little endian?)
while($originalSize > 0){ 
	$byte_array[] = (int) ($originalSize%256);
	$originalSize = (int) ($originalSize/256);
}

// //Process the message in successive 512-bit chunks: = 64 bytes
// So finish off the padding to get to mod 64 bytes:
while(sizeof($byte_array)%64 != 0){
	$byte_array[] = (int) 0;
} 
echo "Number of bytes in padded message: ".sizeof($byte_array)."\n";
echo "Fully padded message:\n";
printHexArray($byte_array);
echo "\n\n";



//-------------------Left Off Here---------------------
// for each 512-bit chunk of message (64 bytes)
$chunk_min_byte = 0;
$chunk_max_byte = 63;


while($chunk_max_byte < sizeof($byte_array)){

	echo "Working on bytes ".$chunk_min_byte." to ".$chunk_max_byte;

	// break chunk into sixteen 32-bit (4 byte)words M[j], 0 ≤ j ≤ 15
	$M = array();	//Reinitialize to a null array since PHP doesn't always scope well.
	echo "\n\nM: \n";
	for($j = 0; $j <= 15; $j++){
		$M[] = hexdec(
				dechex($byte_array[4*$j + 4]).
				dechex($byte_array[4*$j + 3]).
				dechex($byte_array[4*$j + 2]).		
				dechex($byte_array[4*$j + 1]));
		echo "\tM[".(sizeof($M)-1)."]".dechex($M[sizeof($M)-1].", ")."\n";
	}

	// //Initialize hash value for this chunk:
	$A = $a0;    // var int A := a0
	$B = $b0;    // var int B := b0
	$C = $c0;    // var int C := c0
	$D = $d0;    // var int D := d0


	// //Main loop:
	echo "\nEntering For Loop\n";
	for($i = 0; $i <= 0; $i++){			//------------------------------------------Change this back
	//for($i = 0; $i <= 63; $i++){			// for i from 0 to 63
		echo "[i = $i] ";
		if($i <= 15){				// if 0 ≤ i ≤ 15 then
	   	 	$F = ($B & $C) | (~$B & $D);	// F := (B and C) or ((not B) and D)
	   	 	$g = $i;			// g := i
		}
		else if($i <= 31){			// else if 16 ≤ i ≤ 31
		    	$F = ($D & $B) | (~$D & $C);	// F := (D and B) or ((not D) and C)
		    	$g = ((5 * $i) + 1) % 16;	// g := (5×i + 1) mod 16
		}
	
		else if($i <= 47){			// else if 32 ≤ i ≤ 47
		    	$F = ($B ^ $C) ^ $D;		// F := B xor C xor D
		    	$g = (3*$i + 5) % 16;		// g := (3×i + 5) mod 16
		}
		else{ 					// else if 48 ≤ i ≤ 63
		    	$F = $C ^ ($B | ~$D);		// F := C xor (B or (not D))
		    	$g = (7 * $i) % 16;		// g := (7×i) mod 16
		}
	
		$dTemp = $D;				// dTemp := D
		$D = $C;				// D := C
		$C = $B;				// C := B
		$B += leftRotate(($A + $F + $K[$i] + $M[$g]), $shiftAmounts[$g]);
							// B := B + leftrotate((A + F + K[i] + M[g]), s[i])		
		$A = $dTemp;				// A := dTemp
	}// end for
	    
	// //Add this chunk's hash to result so far:
	$a0 += $A;    // a0 := a0 + A	
	$b0 += $B;    // b0 := b0 + B
	$c0 += $C;    // c0 := c0 + C
	$d0 += $D;    // d0 := d0 + D

	echo "a: ".dechex($a0)."\tb: ".dechex($b0)."\tc: ".dechex($c0)."\td: ".dechex($d0)."\n";

	//Increment start and end byte numbers
	$chunk_min_byte += 64;
	$chunk_max_byte += 64;

}// end for (actually a while loop)

// var char digest[16] := a0 append b0 append c0 append d0 //(Output is in little-endian)
echo "Final Result: ".dechex($a0).dechex($b0).dechex($c0).dechex($d0);


// //leftrotate function definition
// leftrotate (x, c)
    // return (x << c) binary or (x >> (32-c));
function leftrotate($x, $c){
	return substr($x, $c).substr($x, 0, $c);
}


/*
 * printHexArray - Function to echo a byte array in hexidecimal notation 
 * 			with each four bytes seperated by a space.
 */
function printHexArray($byteArray){
	$i = 0;
	foreach($byteArray as $byte){
		echo str_pad(dechex($byte), 2, "0", STR_PAD_LEFT) ;
		if($i++ == 3){
			echo " ";
			$i = 0;
		}
	}
	echo "\n";
}


echo "\n";	//Start the command line prompt on a new line.

?>
