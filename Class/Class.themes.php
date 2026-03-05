<?php
class Themes
{
	public function GetThemes($house)
	{
		include 'Themes/'.$house.'.php';
	}
}