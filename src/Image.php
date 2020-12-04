<?php
	namespace Bolt;

	use Exception;

	class Image
	{
		public $image;
		public $info;

		public function load(string $filename): self
		{
			$this->info = getimagesize($filename);

			switch ($this->info['mime'])
			{
				case "image/jpeg":
					$this->image = imagecreatefromjpeg($filename);
					break;
				case "image/gif":
					$this->image = imagecreatefromgif($filename);
					break;
				case "image/png":
					$this->image = imagecreatefrompng($filename);
					break;
			}

			if ($this->image === false)
			{
				throw new Exception("Image not loaded");
			}

			return $this;
		}

		public function save(string $filename, int $permissions = null): self
		{
			switch ($this->info['mime'])
			{
				case "image/jpeg":
					$result = imagejpeg($this->image, $filename);
					break;
				case "image/gif":
					$result = imagegif($this->image, $filename);
					break;
				case "image/png":
					$result = imagepng($this->image, $filename);
					break;
				default:
					throw new Exception("Unable to save to that image type");
					break;
			}

			if ($result === false)
			{
				throw new Exception("Image not saved");
			}

			if ($permissions != null)
			{
				chmod($filename, $permissions);
			}

			return $this;
		}

		public function newImage(int $width, int $height, string $mime)
		{
			$this->image = imagecreatetruecolor($width, $height);
			#imagealphablending($this->image, false);
			#imagesavealpha($this->image, true);
			#imagefill($this->image, 0, 0, imagecolorallocatealpha($this->image, 0, 0, 0, 127));

			$this->info[0] = $width;
			$this->info[1] = $height;
			$this->info['mime'] = $mime;
		}

		public function display(): void
		{
			header("Content-type: " . $this->info['mime']);

			switch ($this->info['mime'])
			{
				case "image/jpeg":
					imagejpeg($this->image, null, 100);
					break;
				case "image/gif":
					imagegif($this->image);
					break;
				case "image/png":
					imagealphablending($this->image, true);
					imagesavealpha($this->image, true);
					imagepng($this->image);
					break;
			}
		}

		public function resizeToWidth(int $width): self
		{
			$ratio = $width / $this->getDimension("x");
			$height = $this->getDimension("y") * $ratio;
			$this->resize($width, $height);

			return $this;
		}

		public function resizeToHeight(int $height): self
		{
			$ratio = $height / $this->getDimension("y");
			$width = $this->getDimension("x") * $ratio;
			$this->resize($width, $height);

			return $this;
		}

		public function ratioResize(int $width, int $height): self
		{
			$imageRatio = $this->ratio();
			$resizeRatio = $width / $height;

			if ($imageRatio >= $resizeRatio)
			{
				$this->resizeToWidth($width);
			}
			else
			{
				$this->resizeToHeight($height);
			}

			return $this;
		}

		public function resize(int $width, int $height): self
		{
			$resizedImage = imagecreatetruecolor($width, $height);

			imagealphablending($resizedImage, false);
			imagesavealpha($resizedImage, true);
			imagefill($resizedImage, 0, 0, imagecolorallocatealpha($resizedImage, 0, 0, 0, 127));
			imagecopyresampled($resizedImage, $this->image, 0, 0, 0, 0, $width, $height, $this->getDimension("x"), $this->getDimension("y"));
			$this->image = $resizedImage;

			$this->info[0] = $width;
			$this->info[1] = $height;

			return $this;
		}

		public function scale(int $percentage): self
		{
			$width = $this->getDimension("x") * ($percentage / 100);
			$height = $this->getDimension("y") * ($percentage / 100);

			$this->resize($width, $height);

			return $this;
		}

		public function getDimension(string $type)
		{
			$result = ($type == "x") ? $this->info[0] : $this->info[1];

			return $result;
		}

		public function crop(int $top, int $right, int $bottom, int $left, int $colour = null): self
		{
			$original = $this->image;
			$x = $this->getDimension("x");
			$y = $this->getDimension("y");

			$newX = $x + ($left + $right);
			$newY = $y + ($top + $bottom);

			$this->newImage($newX, $newY, $this->info['mime']);

			if ($colour === null)
			{
				$colour = imagecolorallocatealpha($this->image, 0, 0, 0, 127);
			}

			imagefill($this->image, 0, 0, $colour);
			imagecopy($this->image, $original, $left, $top, 0, 0, $x, $y);

			return $this;
		}

		public function ratio(): float
		{
			return $this->getDimension("x") / $this->getDimension("y");
		}
	}
?>
