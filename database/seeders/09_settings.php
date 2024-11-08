<?php
return new class implements \Simflex\Core\DB\Seeder
{
    public function seed(): void
    {
        \Simflex\Core\Models\ModelSettings::bulkInsert([
            [
                'npp' => 0,
                'name' => 'Название сайта',
                'alias' => 'site_name',
                'value' => 'Simflex',
                'type' => 'string',
                'enum_values' => ''
            ],
            [
                'npp' => 0,
                'name' => 'Email',
                'alias' => 'email',
                'value' => '',
                'type' => 'string',
                'enum_values' => ''
            ],
            [
                'npp' => 12,
                'name' => 'Телефон',
                'alias' => 'phone',
                'value' => '8 800 555 35 35',
                'type' => 'string',
                'enum_values' => ''
            ],
            [
                'npp' => 11,
                'name' => 'Часы работы',
                'alias' => 'workhours',
                'value' => 'Пн-пт, с 10:00 до 19:00 ',
                'type' => 'string',
                'enum_values' => ''
            ],
            [
                'npp' => 14,
                'name' => 'Email для получения заявок',
                'alias' => 'form_email',
                'value' => '',
                'type' => 'string',
                'enum_values' => ''
            ],
            [
                'npp' => 0,
                'name' => 'Адрес',
                'alias' => 'address',
                'value' => '',
                'type' => 'string',
                'enum_values' => ''
            ],
            [
                'npp' => 0,
                'name' => 'ВКонтакте',
                'alias' => 'vk',
                'value' => '',
                'type' => 'string',
                'enum_values' => ''
            ],
            [
                'npp' => 0,
                'name' => 'Телеграм',
                'alias' => 'tg',
                'value' => '',
                'type' => 'string',
                'enum_values' => ''
            ],
            [
                'npp' => 0,
                'name' => 'Инстаграм',
                'alias' => 'inst',
                'value' => '',
                'type' => 'string',
                'enum_values' => ''
            ],
            [
                'npp' => 13,
                'name' => 'WhatsApp',
                'alias' => 'whats_app',
                'value' => '',
                'type' => 'string',
                'enum_values' => ''
            ],
            [
                'npp' => 1,
                'name' => 'Организация',
                'alias' => 'company',
                'value' => '',
                'type' => 'string',
                'enum_values' => ''
            ],
            [
                'npp' => 1,
                'name' => 'Токен Telegram для получения заявок',
                'alias' => 'form_tg_token',
                'value' => '',
                'type' => 'string',
                'enum_values' => ''
            ],
            [
                'npp' => 1,
                'name' => 'Chat ID для получения заявок',
                'alias' => 'form_tg_chat_id',
                'value' => '',
                'type' => 'string',
                'enum_values' => ''
            ],
            [
                'npp' => 0,
                'name' => 'Одноклассники',
                'alias' => 'ok',
                'value' => '',
                'type' => 'string',
                'enum_values' => ''
            ],
            [
                'npp' => 0,
                'name' => 'Адрес офиса',
                'alias' => 'office_address',
                'value' => '',
                'type' => 'string',
                'enum_values' => ''
            ],
            [
                'npp' => 0,
                'name' => 'Ссылка на Яндекс карту',
                'alias' => 'map',
                'value' => '',
                'type' => 'string',
                'enum_values' => ''
            ],
        ]);
    }
};