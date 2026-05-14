<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'modulo_id',
        'titulo',
        'tipo',
        'url',
        'orden',
    ];

    public function modulo()
    {
        return $this->belongsTo(Modulo::class);
    }

    public function progresos()
    {
        return $this->hasMany(ProgresoMaterial::class);
    }

    public function getVideoEmbedUrlAttribute()
    {
        $url = $this->url;

        if (empty($url)) {
            return null;
        }

        if (str_contains($url, 'drive.google.com')) {
            $embedUrl = str_replace('/view', '/preview', $url);
            $embedUrl = str_replace('/edit', '/preview', $embedUrl);
            return $embedUrl;
        }

        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s ]{11})/i';
            if (preg_match($pattern, $url, $matches)) {
                return "https://www.youtube.com/embed/" . $matches[1] . "?enablejsapi=1&rel=0&modestbranding=1";
            }
        }

        if (str_contains($url, 'player.vimeo.com/video/')) {
            return $url;
        }

        if (str_contains($url, 'vimeo.com/')) {
            if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
                return "https://player.vimeo.com/video/" . $matches[1];
            }
        }

        return null;
    }

    public function getEsVideoValidoAttribute()
    {
        if ($this->tipo !== 'video' || empty($this->url)) {
            return false;
        }

        if (str_contains($this->url, 'drive.google.com')) {
            return true;
        }

        if (str_contains($this->url, 'youtube.com') || str_contains($this->url, 'youtu.be')) {
            $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s ]{11})/i';
            return (bool) preg_match($pattern, $this->url);
        }

        if (str_contains($this->url, 'vimeo.com')) {
            return (bool) preg_match('/vimeo\.com\/(\d+)/', $this->url);
        }

        return false;
    }

    public function getVideoPlatformAttribute()
    {
        $url = $this->url ?? '';

        if (str_contains($url, 'drive.google.com')) {
            return 'google-drive';
        }

        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            return 'youtube';
        }

        if (str_contains($url, 'vimeo.com')) {
            return 'vimeo';
        }

        return 'unknown';
    }
}
