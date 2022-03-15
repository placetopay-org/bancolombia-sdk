<?php

namespace PlacetoPay\BancolombiaSDK\Support;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use PlacetoPay\Flamingo\Support\Mock\ClientMock;
use PlacetoPay\Tangram\Entities\Cache;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsResolver
{
    public static function create(array $settings): OptionsResolver
    {
        $resolver = new OptionsResolver();

        $resolver->define('settings')
            ->required()
            ->default(function (OptionsResolver $settingResolver) {
                $settingResolver->define('terminalNumber')
                    ->required()
                    ->allowedTypes('string')
                    ->default(null);

                $settingResolver->define('merchantCode')
                    ->required()
                    ->allowedTypes('string')
                    ->default(null);
            });

        $resolver->define('endpoints')
            ->required()
            ->default(function (OptionsResolver $resolve) {
                $resolve->define('loginSwitchURL')
                    ->required()
                    ->allowedTypes('string');
                $resolve->define('valClientSwitchURL')
                    ->required()
                    ->allowedTypes('string');
                $resolve->define('transactionalSwitchURL')
                    ->required()
                    ->allowedTypes('string');
            });

        $resolver->define('credentials')
            ->required()
            ->default(function (OptionsResolver $resolve) {
                $resolve->define('loginSwitchUser')
                    ->required()->allowedTypes('string');
                $resolve->define('loginSwitchPassword')
                    ->required()->allowedTypes('string');

                $resolve->define('valClientSwitchUser')
                    ->required()->allowedTypes('string');
                $resolve->define('valClientSwitchPassword')
                    ->required()->allowedTypes('string');
            });

        if (isset($settings['logger'])) {
            $resolver->define('logger')->default(function (OptionsResolver $loggerResolver) {
                $loggerResolver->define('name')->allowedTypes('string');
                $loggerResolver->define('via')->required()->allowedTypes(LoggerInterface::class);
                $loggerResolver->define('path')->allowedTypes('string', 'null');
                $loggerResolver->define('debug')->allowedTypes('bool')->default(false);
            });
        }

        $resolver->define('client')
            ->allowedTypes(ClientInterface::class)
            ->default(function (Options $options) {
                if ($options['simulatorMode']) {
                    return new ClientMock();
                }

                return new Client();
            });

        $resolver->define('encryption')
            ->default(function (OptionsResolver $resolver) use ($settings) {
                $resolver->define('enable')
                    ->allowedTypes('bool')->default(true);

                $resolver->define('serverCertificatePublicFile')->allowedTypes('string');
                $resolver->define('clientCertificatePrivateFile')->allowedTypes('string');
                $resolver->define('clientCertificatePublicFile')->allowedTypes('string');

                if ($settings['encryption']['enable'] ?? false) {
                    $resolver->setRequired([
                        'serverCertificatePublicFile',
                        'clientCertificatePrivateFile',
                        'clientCertificatePublicFile',
                    ]);

                    $resolver->addAllowedValues('serverCertificatePublicFile', function ($value) {
                        return file_exists($value);
                    });

                    $resolver->addAllowedValues('clientCertificatePrivateFile', function ($value) {
                        return file_exists($value);
                    });

                    $resolver->addAllowedValues('clientCertificatePublicFile', function ($value) {
                        return file_exists($value);
                    });
                }
            });

        $resolver->define('timeout')
            ->allowedTypes('float', 'int')->default(10);

        $resolver->define('authenticationTokenCacheTime')
            ->allowedTypes('int')->default(43200);

        $resolver->define('cache')
            ->allowedTypes(CacheInterface::class)
            ->default(new Cache());

        $resolver->define('loginAuthorizedIP')
            ->allowedTypes('string')
            ->default('172.16.152.125');

        $resolver->define('transactionalSwitchChannel')
            ->allowedTypes('string')
            ->default('13');

        $resolver->define('loginSwitchChannel')
            ->allowedTypes('string')
            ->default('13');

        $resolver->define('valClientSwitchChannel')
            ->allowedTypes('string')
            ->default('02');

        $resolver->define('simulatorMode')
            ->allowedTypes('bool')->default(false);

        $resolver->define('provider')->allowedTypes('string')->default('Flamingo');

        return $resolver;
    }
}
