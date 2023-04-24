/**
 * @package system-settings
 */
import './component/sw-settings-mailer-smtp';
import './page/sw-settings-mailer';

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
Shopware.Module.register('sw-settings-mailer', {
    type: 'core',
    name: 'settings-mailer',
    title: 'sw-settings-mailer.general.mainMenuItemGeneral',
    description: 'sw-settings-mailer.general.description',
    color: '#9AA8B5',
    icon: 'regular-cog',
    favicon: 'icon-module-settings.png',

    routes: {
        index: {
            component: 'sw-settings-mailer',
            path: 'index',
            meta: {
                parentPath: 'sw.settings.index.system',
                privilege: 'system.system_config',
            },
        },
    },

    settingsItem: {
        group: 'system',
        to: 'sw.settings.mailer.index',
        icon: 'regular-envelope',
        privilege: 'system.system_config',
    },
});
