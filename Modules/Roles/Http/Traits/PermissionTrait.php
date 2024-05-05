<?php

namespace Modules\Roles\Http\Traits;

trait PermissionTrait
{
    public static function getData()
    {
        return collect([
            [
                'type' => 'manage-user',
                'title' => 'view-users'
            ],
            [
                'type' => 'manage-user',
                'title' => 'create-user'
            ],
            [
                'type' => 'manage-user',
                'title' => 'edit-user'
            ],
            [
                'type' => 'manage-user',
                'title' => 'delete-user'
            ],
            [
                'type' => 'manage-user',
                'title' => 'change-status-user'
            ],

            [
                'type' => 'manage-role',
                'title' => 'view-roles'
            ],
            [
                'type' => 'manage-role',
                'title' => 'manage-permissions'
            ],
            [
                'type' => 'manage-role',
                'title' => 'create-role'
            ],
            [
                'type' => 'manage-role',
                'title' => 'edit-role'
            ],
            [
                'type' => 'manage-role',
                'title' => 'delete-role'
            ],
            [
                'type' => 'manage-role',
                'title' => 'change-status-role'
            ],

            // Manage language
            [
                'type' => 'manage-language',
                'title' => 'view-language'
            ],
            
            [
                'type' => 'manage-language',
                'title' => 'create-language'
            ],
            [
                'type' => 'manage-language',
                'title' => 'edit-language'
            ],
            [
                'type' => 'manage-language',
                'title' => 'delete-language'
            ],

            [
                'type' => 'manage-language',
                'title' => 'change-status-language'
            ],

            // Manage Local
            [
                'type' => 'manage-locale',
                'title' => 'view-locale'
            ],
            
            [
                'type' => 'manage-locale',
                'title' => 'create-locale'
            ],
            [
                'type' => 'manage-locale',
                'title' => 'edit-locale'
            ],
            [
                'type' => 'manage-locale',
                'title' => 'delete-locale'
            ],

            [
                'type' => 'manage-locale',
                'title' => 'change-status-locale'
            ],


            // Manage Setting
            [
                'type' => 'manage-setting',
                'title' => 'view-setting'
            ],
            
            
            [
                'type' => 'manage-setting',
                'title' => 'edit-setting'
            ],


            // Manage Service
            [
                'type' => 'manage-service',
                'title' => 'view-service'
            ],
            
            [
                'type' => 'manage-service',
                'title' => 'create-service'
            ],
            [
                'type' => 'manage-service',
                'title' => 'edit-service'
            ],
            [
                'type' => 'manage-service',
                'title' => 'delete-service'
            ],

            [
                'type' => 'manage-service',
                'title' => 'change-status-service'
            ],


            // Manage client
            [
                'type' => 'manage-client',
                'title' => 'view-client'
            ],
            
            [
                'type' => 'manage-client',
                'title' => 'create-client'
            ],
            [
                'type' => 'manage-client',
                'title' => 'edit-client'
            ],
            [
                'type' => 'manage-client',
                'title' => 'delete-client'
            ],

            [
                'type' => 'manage-client',
                'title' => 'change-status-client'
            ],

            // Manage client
            [
                'type' => 'manage-driver',
                'title' => 'view-driver'
            ],
            
            [
                'type' => 'manage-driver',
                'title' => 'create-driver'
            ],
            [
                'type' => 'manage-driver',
                'title' => 'edit-driver'
            ],
            [
                'type' => 'manage-driver',
                'title' => 'delete-driver'
            ],

            [
                'type' => 'manage-driver',
                'title' => 'change-status-driver'
            ],

            // Manage page
            [
                'type' => 'manage-page',
                'title' => 'view-pages'
            ],
            
            [
                'type' => 'manage-page',
                'title' => 'create-page'
            ],
            [
                'type' => 'manage-page',
                'title' => 'edit-page'
            ],
            [
                'type' => 'manage-page',
                'title' => 'delete-page'
            ],

            [
                'type' => 'manage-page',
                'title' => 'change-status-page'
            ],

            // Manage faq
            
            [
                'type' => 'manage-faq',
                'title' => 'view-faqs'
            ],
            [
                'type' => 'manage-faq',
                'title' => 'create-faq'
            ],
            [
                'type' => 'manage-faq',
                'title' => 'edit-faq'
            ],
            [
                'type' => 'manage-faq',
                'title' => 'delete-faq'
            ],
            [
                'type' => 'manage-faq',
                'title' => 'change-status-faq'
            ],
        ]);
    }
}
