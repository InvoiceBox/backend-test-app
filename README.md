## Тестовое задание
### Задача
Разработать API по работе с заказами интернет-магазина. 

Согласно требованиям ТЗ, заказ создается в два этапа: 
1) Создание пустого заказа
2) Добавление в заказ товаров поштучно

Набор данных в заказе:
- идентификатор заказа
- идентификатор пользователя
- сумма заказа
- дата создания заказа

Набор данных в товаре:
- артикул
- название
- стоимость единицы товара
- кол-во единиц товара
- суммарная стоимость всех единиц товара

От API требуются следующие возможности:
- [ ] Создание заказа и привязка его к текущему пользователю (см. [Авторизация](#Авторизация))
- [ ] Добавление в заказ нового товара (необходим пересчет итоговой суммы заказа)
- [ ] Удаление из заказа товаров (необходим пересчет итоговой суммы заказа)
- [ ] Редактирование заказа
- [ ] Удаление заказа
- [ ] Получение списка заказов

### Оформление работы
- Результат необходимо оформить в виде Pull Request в этот репозиторий.
- Разработка должна вестись на основе [Бойлерплейта](#Бойлерплейт), применяя используемые в нем библиотеки. В бойлерплейте есть примеры оформления API для сущности `/example`
- Необходимо написать тесты на реализованные ресурсы. В бойлерплейте есть примеры оформления.
- Необходимо предусмотреть проверки на консистентность данных в заказе

## Бойлерплейт
### Структура проекта

Проект разделен на 4 слоя (своя трактовка слоистой архитектуры):

#### Presentation

Слой точек взаимодействия с приложениями с конечным пользователем. Основные сущности: контроллеры и консольные команды. В этом слое не должно
быть бизнес логики, а типовыми задачами является:

- валидация входящих данных
- десериализация/сериализация входящих/исходящих данных
- запуск обработчиков Application слоя

#### Infrastructure

Слой сервисов для работы с внешними приложениями, например:

- репозитории работы с базами данных
- http клиенты по работе с внешними сервисами по API
- RPC клиенты

#### Application

Слой реализации бизнес логики. В нем используются модели из Domain слоя, а так же обработчики из
Infrastructure.

#### Domain

Слой, где находятся модели бизнес логики.

### Запуск приложения локально

Для локального запуска проекта требуется:

- composer (php8)
- docker
- bash интерпретатор

Для запуска выполните команду: `./build/run-local.sh`, которая через docker-compose запустит следующие
контейнеры:

- nginx - `localhost:6082`
- php - php-fpm сервер
- pg-test - база данных для выполнения тестов (`postgresql://test:test@localhost:5944/test`)
- pg - локальная база данных приложения (`postgresql://db_user:db_password@127.0.0.1:5943/backend-test-app`)

### Тесты

- основной файл настроек тестового окружения находится в корне проекта в файле phpunit.xml
- ручной запуск тестов `php ./vendor/phpunit/phpunit/phpunit --configuration ./phpunit.xml`

### Роутинг

Основные настройки роутинга находятся в `config/routes.yaml`. Так же тут прописаны группы сериализации по умолчанию.

### Авторизация

Авторизация тестового приложения реализована через проверку заголовка `X-USER-ID` 

### Группы сериализации

Для заполнения полей объектов данными из http запросов(json) используется компонент symfony/serializer. Для каждого поля
класса можно задать свой список групп сериализации, напрмиер для JMS Serializer через аннотацию `@JMS\Serializer\Annotation\Groups`. 

#### Рассмотрим на примере следующего класса:

```php
use JMS\Serializer\Annotation\Groups;

class Example
{
  /**
  * @Groups({"read"})
  */
  private int $id;
  
  /**
  * @Groups({"read", "create"})
  */
  private string $title;
  
  /**
  * @Groups({"read", "update"})
  */
  private string $description;
}
```
Заполнение полей объекта из json:
```json
{"id":123, "title": "Example title", "description": "test"}
```
```php
$data = $serializer->deserialize('{"id":123, "title": "Example title", "description": "test"}', Example::class, 'json', DeserializationContext::create()->setGroups(['create']));
// Создаст экземпляр класса только с одним заполненным полем - title

$data = $serializer->deserialize('{"id":123, "title": "Example title", "description": "test"}', Example::class, 'json', DeserializationContext::create()->setGroups(['update']));
// Создаст экземпляр класса только с одним заполненным полем - description

$data = $serializer->deserialize('{"id":123, "title": "Example title", "description": "test"}', Example::class, 'json', DeserializationContext::create()->setGroups(['read']));
// Создаст экземпляр класса с заполненными полями: id, title и description
```

Получение json представление объекта:
```php
$example = new Example();
$example->setId(1);
$example->setTitle('Title');
$example->setDescription('Description');

$data = $serializer->serialize($example, 'json', SerializationContext::create()->setGroups(['read']));
// {"id":1, "title": "Title", "description": "Description"}
```


### Основные библиотеки:
- Валидация - https://symfony.com/doc/current/validation.html
- Сериализация / десериализация / нормализация данных - https://jmsyst.com/libs/serializer
- Работа с базой данных
  - https://symfony.com/doc/current/doctrine.html
  - https://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html
- Логирование https://symfony.com/doc/current/logging.html
- Роутинг https://symfony.com/doc/current/routing.html
- Тестирование:
  - https://symfony.com/doc/current/testing.html
  - https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html
