#!/usr/bin/perl
print "Content-type: text/html\n\n";

if ($ENV{'REQUEST_METHOD'} ne 'POST')
{
     print <<"HTML";
     <HTML>
     <BODY>
     only method POST is supported here.
     </BODY>
     </HTML>

HTML

     exit;
}

read(STDIN, $buffer, $ENV{'CONTENT_LENGTH'});
@pairs = split(/&/, $buffer);
foreach $pair (@pairs)
       {
	($name, $value) = split(/=/, $pair);
	$value =~ tr/+/ /;
	$value =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
	print $value;
	if($name eq "f")
		{
		print "<textarea name=TextBox1>"
		}
	if($name eq "g")
		{
		print "</textarea><textarea name=TextBox2>"
		}
	if($name eq "h")
		{
		print "</textarea>"
		}
    }

exit;
 
