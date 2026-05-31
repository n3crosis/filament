<?php

namespace Filament\Support\Assets;

use Closure;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Css extends Asset
{
    protected string | Htmlable | Closure | null $html = null;

    protected ?string $relativePublicPath = null;

    public function html(string | Htmlable | Closure | null $html): static
    {
        $this->html = $html;

        return $this;
    }

    public function relativePublicPath(?string $relativePublicPath): static
    {
        $this->relativePublicPath = $relativePublicPath;

        return $this;
    }

    public function getHref(): string
    {
        if ($this->isRemote()) {
            return $this->getPath();
        }

        return asset($this->getRelativePublicPath()) . '?v=' . $this->getVersion();
    }

    public function getHtml(): Htmlable
    {
        $html = value($this->html);

        if (str($html)->contains('<link')) {
            $htmlString = $html instanceof Htmlable ? $html->toHtml() : (string) $html;
            $cspNonce = FilamentAsset::renderCspNonce()->toHtml();

            if (filled($cspNonce)) {
                $htmlString = (string) preg_replace('/<link\b/i', '<link ' . $cspNonce, $htmlString);
            }

            return new HtmlString($htmlString);
        }

        $html ??= $this->getHref();

        return new HtmlString("<link
            href=\"{$html}\"
            rel=\"stylesheet\"
            " . FilamentAsset::renderCspNonce()->toHtml() . "
            data-navigate-track
        />");
    }

    public function getRelativePublicPath(): string
    {
        if (filled($this->relativePublicPath)) {
            return $this->relativePublicPath;
        }

        $path = config('filament.assets_path', '');

        return ltrim("{$path}/css/{$this->getPackage()}/{$this->getId()}.css", '/');
    }

    public function getPublicPath(): string
    {
        return public_path($this->getRelativePublicPath());
    }
}
