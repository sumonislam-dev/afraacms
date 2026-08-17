<?php

if (! function_exists('theme_color_shades')) {
    /**
     * Derive a full 50-900 Tailwind-style shade ramp from a single hex
     * color, so the Settings > Branding screen only needs one color picker
     * instead of nine. The 500 shade is the input color, unchanged; lighter
     * shades blend toward white, darker shades blend toward black - plain
     * RGB interpolation, not HSL, so it holds up for any input hue/saturation
     * without producing odd tints.
     *
     * @return array<string, string> keyed '50'..'900', e.g. ['50' => '#fef3ec', ..., '500' => '#f96d00', ..., '900' => '#622b00']
     */
    function theme_color_shades(string $hex): array
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $mixToward = function (int $target, float $ratio) use ($r, $g, $b): string {
            $channel = fn (int $c): int => (int) round($c * (1 - $ratio) + $target * $ratio);

            return sprintf('#%02x%02x%02x', $channel($r), $channel($g), $channel($b));
        };

        $shades = [];

        foreach (['50' => 0.95, '100' => 0.90, '200' => 0.75, '300' => 0.60, '400' => 0.30] as $step => $ratio) {
            $shades[$step] = $mixToward(255, $ratio);
        }

        $shades['500'] = sprintf('#%02x%02x%02x', $r, $g, $b);

        foreach (['600' => 0.15, '700' => 0.30, '800' => 0.45, '900' => 0.60] as $step => $ratio) {
            $shades[$step] = $mixToward(0, $ratio);
        }

        return $shades;
    }
}
