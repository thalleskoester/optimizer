<?php

namespace ThallesDella\Optimizer\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use ThallesDella\Optimizer\Optimizer;

class OptimizerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('Optimizer', function () {
            return new Optimizer();
        });
        
        $this->mergeConfigFrom(
            dirname(__DIR__, 2) . '/config/optimizer.php', 'optimizer'
        );
    }
    
    public function boot()
    {
        $this->publishes([
            dirname(__DIR__, 2) . '/config/optimizer.php' => config_path('optimizer.php'),
        ]);
        
        Optimizer::openGraph(
            Config::get('optimizer.site.name', Config::get('app.name')),
            $this->app->getLocale(),
            Config::get('optimizer.site.schema', 'article')
        );
        
        if (Config::get('optimizer.fb.auto', false)) {
            Optimizer::publisher(
                Config::get('optimizer.fb.page', null),
                Config::get('optimizer.fb.author', null)
            );
            
            if (Config::get('optimizer.fb.id', false)) {
                Optimizer::facebook(Config::get('optimizer.fb.id'));
            } elseif (Config::get('optimizer.fb.admins', false)) {
                Optimizer::facebook(null, Config::get('optimizer.fb.admins'));
            }
        }
        
        if (Config::get('optimizer.twitter.auto', false)) {
            Optimizer::twitterCard(
                Config::get('optimizer.twitter.creator', null),
                Config::get('optimizer.site.name', null),
                Config::get('url', null),
                Config::get('optimizer.twitter.card', 'summary_large_image')
            );
        }
        
    }
}