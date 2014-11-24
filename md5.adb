with Ada.Text_IO;
use Ada.Text_IO;

-- md5.adb
-- Author: C2C Justin Niquette
-- Date:   23 Nov 2014
-- Class:  CS431/T5A



procedure md5 is

   type U32 is mod 16#FFFFFFFF#;

   Shift_Amounts : array(0..63) of Positive;
   K: array(0..63) of U32;


   --Initialize Variables
   a0 : U32 := 16#67452301#;   --A
   b0 : U32 := 16#efcdab89#;   --B
   c0 : U32 := 16#98badcfe#;   --C
   d0 : U32 := 16#10325476#;   --D


   function Get_String return String is -- This function from rosettacode.org
      Line : String (1 .. 1_000);
      Last : Natural;
   begin
      Get_Line (Line, Last);
      return Line (1 .. Last);
   end Get_String;



begin

   -- Define the per-round shift amounts
   Shift_Amounts (0..63) :=
     ( 7, 12, 17, 22,  7, 12, 17, 22,  7, 12, 17, 22,  7, 12, 17, 22,
       5,  9, 14, 20,  5,  9, 14, 20,  5,  9, 14, 20,  5,  9, 14, 20,
       4, 11, 16, 23,  4, 11, 16, 23,  4, 11, 16, 23,  4, 11, 16, 23,
       6, 10, 15, 21,  6, 10, 15, 21,  6, 10, 15, 21,  6, 10, 15, 21 );

   K (0..63) :=
     (16#d76aa478#, 16#e8c7b756#, 16#242070db#, 16#c1bdceee#,
      16#f57c0faf#, 16#4787c62a#, 16#a8304613#, 16#fd469501#,
      16#698098d8#, 16#8b44f7af#, 16#ffff5bb1#, 16#895cd7be#,
      16#6b901122#, 16#fd987193#, 16#a679438e#, 16#49b40821#,
      16#f61e2562#, 16#c040b340#, 16#265e5a51#, 16#e9b6c7aa#,
      16#d62f105d#, 16#02441453#, 16#d8a1e681#, 16#e7d3fbc8#,
      16#21e1cde6#, 16#c33707d6#, 16#f4d50d87#, 16#455a14ed#,
      16#a9e3e905#, 16#fcefa3f8#, 16#676f02d9#, 16#8d2a4c8a#,
      16#fffa3942#, 16#8771f681#, 16#6d9d6122#, 16#fde5380c#,
      16#a4beea44#, 16#4bdecfa9#, 16#f6bb4b60#, 16#bebfbc70#,
      16#289b7ec6#, 16#eaa127fa#, 16#d4ef3085#, 16#04881d05#,
      16#d9d4d039#, 16#e6db99e5#, 16#1fa27cf8#, 16#c4ac5665#,
      16#f4292244#, 16#432aff97#, 16#ab9423a7#, 16#fc93a039#,
      16#655b59c3#, 16#8f0ccc92#, 16#ffeff47d#, 16#85845dd1#,
      16#6fa87e4f#, 16#fe2ce6e0#, 16#a3014314#, 16#4e0811a1#,
      16#f7537e82#, 16#bd3af235#, 16#2ad7d2bb#, 16#eb86d391#);
   --Shift_Amounts(63) := 69;


   --  Test := Integer'Value(Shift_Amounts'First);

   --Note: All variables are unsigned 32 bit and wrap modulo 2^32 when calculating
   --var int[64] s, K
   --Put(Integer'Image(Shift_Amounts'Last));
   For i in 1..Shift_Amounts'Length-1 loop
      Put(Integer'Image(Shift_Amounts(i)));
      Put(U32'Image(K(i)));
   end loop;


   Put(Get_String);

end md5;



--
--  --Pre-processing: adding a single 1 bit
--  --append "1" bit to message
--  -- Notice: the input bytes are considered as bits strings,
--  --  where the first bit is the most significant bit of the byte.[47]
--
--
--  --Pre-processing: padding with zeros

--  append original length in bits mod (2 pow 64) to message
--
--
--  --Process the message in successive 512-bit chunks:
--  for each 512-bit chunk of message

--  --Initialize hash value for this chunk:
--      var int A := a0
--      var int B := b0
--      var int C := c0
--      var int D := d0
--  --Main loop:
--      for i from 0 to 63

--              F := (B and C) or ((not B) and D)
--              g := i

--              F := (D and B) or ((not D) and C)
--              g := (5×i + 1) mod 16

--              F := B xor C xor D
--              g := (3×i + 5) mod 16

--              F := C xor (B or (not D))
--              g := (7×i) mod 16
--          dTemp := D
--          D := C
--          C := B
--          B := B + leftrotate((A + F + K[i] + M[g]), s[i])
--          A := dTemp
--      end for
--  --Add this chunk's hash to result so far:
--      a0 := a0 + A
--      b0 := b0 + B
--      c0 := c0 + C
--      d0 := d0 + D
--  end for
--
--  var char digest[16] := a0 append b0 append c0 append d0 --(Output is in little-endian)
--
--  --leftrotate function definition
--  leftrotate (x, c)
--      return (x << c) binary or (x >> (32-c));
