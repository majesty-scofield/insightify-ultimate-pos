<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Models\System;
use App\Utils\Util;
use Illuminate\Routing\Controller;
use Menu;
use Modules\Superadmin\Notifications\NewBusinessNotification;
use Modules\Superadmin\Notifications\NewBusinessWelcomNotification;
use Notification;

class DataController extends Controller
{
    /**
     * Parses notification message from database.
     *
     * @return array
     */
    public function parse_notification($notification)
    {
        $notification_data = [];
        if ($notification->type ==
            'Modules\Superadmin\Notifications\SendSubscriptionExpiryAlert') {
            $data = $notification->data;
            $msg = __('superadmin::lang.subscription_expiry_alert', ['days_left' => $data['days_left'], 'app_name' => config('app.name')]);

            $notification_data = [
                'msg' => $msg,
                'icon_class' => 'fas fa-exclamation-triangle bg-yellow',
                'link' => action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'index']),
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans(),
            ];
        } elseif ($notification->type ==
            'Modules\Superadmin\Notifications\SuperadminCommunicator') {
            $msg = __('superadmin::lang.new_message_from_superadmin');

            $notification_data = [
                'msg' => $msg,
                'icon_class' => 'fas fa-exclamation-triangle bg-yellow',
                'link' => action([\App\Http\Controllers\HomeController::class, 'showNotification'], [$notification->id]),
                'show_popup' => true,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans(),
            ];
        }

        return $notification_data;
    }

    /**
     * Function to be called after a new business is created.
     *
     * @return null
     */
    public function after_business_created($data)
    {
        try {
            //Send new registration notification to superadmin
            $is_notif_enabled =
            System::getProperty('enable_new_business_registration_notification');

            $common_util = new Util();

            if (!$common_util->IsMailConfigured()) {
                return null;
            }

            $email = System::getProperty('email');
            $business = $data['business'];

            if (!empty($email) && $is_notif_enabled == 1) {
                Notification::route('mail', $email)
                    ->notify(new NewBusinessNotification($business));
            }

            //Send welcome email to business owner
            $welcome_email_settings = System::getProperties(['enable_welcome_email', 'welcome_email_subject', 'welcome_email_body'], true);

            if (isset($welcome_email_settings['enable_welcome_email']) && $welcome_email_settings['enable_welcome_email'] == 1 && !empty($welcome_email_settings['welcome_email_subject']) && !empty($welcome_email_settings['welcome_email_body'])) {
                $subject = $this->removeTags($welcome_email_settings['welcome_email_subject'], $business);
                $body = $this->removeTags($welcome_email_settings['welcome_email_body'], $business);

                $welcome_email_data = [
                    'subject' => $subject,
                    'body' => $body,
                ];

                Notification::route('mail', $business->owner->email)
                    ->notify(new NewBusinessWelcomNotification($welcome_email_data));
            }
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
        }

        return null;
    }

    private function removeTags($string, $business)
    {
        $string = str_replace('{business_name}', $business->name, $string);
        $string = str_replace('{owner_name}', $business->owner->user_full_name, $string);

        return $string;
    }

    /**
     * Adds Superadmin menus
     *
     * @return null
     */
    public function modifyAdminMenu()
    {
        if (auth()->user()->can('superadmin')) {
            Menu::modify(
                'admin-sidebar-menu',
                function ($menu) {
                    $menu->url(action([\Modules\Superadmin\Http\Controllers\SuperadminController::class, 'index']), __('superadmin::lang.superadmin'), ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                    <path d="M6 21v-2a4 4 0 0 1 4 -4h2.5"></path>
                    <path d="M19.001 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                    <path d="M19.001 15.5v1.5"></path>
                    <path d="M19.001 21v1.5"></path>
                    <path d="M22.032 17.25l-1.299 .75"></path>
                    <path d="M17.27 20l-1.3 .75"></path>
                    <path d="M15.97 17.25l1.3 .75"></path>
                    <path d="M20.733 20l1.3 .75"></path>
                  </svg>', 'style' => config('app.env') == 'demo' ? 'background-color: #12DDDC;color:white' : '', 'active' => request()->segment(1) == 'superadmin'])->order(1);
                }
            );
        }

        if (auth()->user()->can('superadmin.access_package_subscriptions') && auth()->user()->can('business_settings.access')) {
            $menu = Menu::instance('admin-sidebar-menu');
            $menu->whereTitle(__('business.settings'), function ($sub) {
                $sub->url(
                    action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'index']),
                    __('superadmin::lang.subscription'),
                    ['icon' => '', 'active' => request()->segment(1) == 'subscription']
                );
            });
        }
    }

    /**
     * Defines user permissions for the module.
     *
     * @return array
     */
    public function user_permissions()
    {
        return [
            [
                'value' => 'superadmin.access_package_subscriptions',
                'label' => __('superadmin::lang.access_package_subscriptions'),
                'default' => false,
            ],
        ];
    }
}
