<?php

class Markdown
{
	// A string that is not delimited by whitespaces, and does not contain newlines.
	static private $segment = "(?!\s)([^\\n\\r]+)(?<!\s)";
	static private $bracketSegment = "(?!\s)([^\\n\\r\]]+)(?<!\s)";
	static private $newline = "\\r?\\n";

	/** @var array $REGEXES */
	static private $REGEXES = array();

	static private function AddRegex($pattern, $sub) {
		self::$REGEXES["/".$pattern."/U"] = $sub;
	}

	static public function __static_construct() {
		// Standard Markdown stuff
		self::AddRegex("\*\*".self::$segment."\*\*",  "<b>$1</b>");
		self::AddRegex("__".self::$segment."__",      "<u>$1</u>");
		self::AddRegex("~~".self::$segment."~~",      "<s>$1</s>");
		self::AddRegex("\*".self::$segment."\*",      "<i>$1</i>");
		self::AddRegex("_".self::$segment."_",        "<i>$1</i>");
		self::AddRegex("_".self::$segment."_",        "<i>$1</i>");
		self::AddRegex("\[".self::$bracketSegment."\]\(".self::$segment."\)", "<a href=\"$2\">$1</a>");

		// ArtArchive custom tag links
		self::AddRegex("\[\[".self::$bracketSegment."\|".self::$bracketSegment."\]\]", "<a class=tag href=\"/tag/$1/\">$2</a>");
		self::AddRegex("\[\[".self::$bracketSegment."\]\]",                     "<a class=tag href=\"/tag/$1/\">$1</a>");

		// Newline handling
		self::AddRegex("(\\r?\\n)(\\r?\\n)+", "<br/><br/>");
		self::AddRegex("  \\r?\\n", "<br/>");

		// Test stuff
		// self::AddRegex("(?<=\\n)\\r?\\n", "<br/>");
		// self::AddRegex("\\n", "<br/>");
		// var_dump(self::$REGEXES);
	}

	static public function MarkdownToHtml($string) : string {
		foreach (self::$REGEXES as $match => $subst){
			$string = preg_replace($match, $subst, $string);
		}
		return $string;
	}
}

Markdown::__static_construct()

?>