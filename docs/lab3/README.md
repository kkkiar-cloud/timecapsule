# Отчёт по 3 лабораторной работе AWS

## 1. 
- ФИО: Кайнар Анна
- Группа: IA2403
- Выбранный уровень: Продвинутый
- k = 30
- Репозиторий: https://github.com/kkkiar-cloud/timecapsule
---

## 2. Задания базового уровня


### Задание 1. План адресов


| Подсеть | Зона доступности | Диапазон | Первый и последний доступный адрес | Количество адресов |
|---|---|---|---|---:|
| public-a | eu-central-1a | 10.0.0.0/24 | 10.0.0.4 – 10.0.0.254 | 251 |
| public-b | eu-central-1b | 10.0.1.0/24 | 10.0.1.4 – 10.0.1.254 | 251 |
| private-a | eu-central-1a | 10.0.10.0/24 | 10.0.10.4 – 10.0.10.254 | 251 |
| private-b | eu-central-1b | 10.0.11.0/24 | 10.0.11.4 – 10.0.11.254 | 251 |


### Задание 2. VPC и подсети

<img width="2060" height="262" alt="l1" src="https://github.com/user-attachments/assets/6cf748e0-4eac-489d-ac88-dbcaf3b4fca4" />

<img width="2154" height="1054" alt="l2" src="https://github.com/user-attachments/assets/06fad62a-0b50-4791-8055-47418c9c2dc1" />

### Задание 3. Internet Gateway и таблицы маршрутов

<img width="2559" height="1228" alt="Снимок экрана 2026-10-12 214030" src="https://github.com/user-attachments/assets/483d40ba-5eaa-4ded-85c8-7ba7c926640b" />
**Рисунок 2.** Таблица маршрутов `timecapsule-dev-public-rt`: маршрут `10.0.0.0/16` остаётся локальным, а `0.0.0.0/0` направлен через Internet Gateway.

<img width="2189" height="715" alt="Снимок экрана 2026-10-14 230025" src="https://github.com/user-attachments/assets/ea6e06e5-b89c-4039-88c6-27b3d1a4c8ef" />
**Рисунок 3.** Таблица маршрутов `timecapsule-dev-private-rt` и её связь с приватными подсетями.

<img width="2197" height="741" alt="Снимок экрана 2026-10-14 230058" src="https://github.com/user-attachments/assets/0a9d3628-87df-4d8c-b332-abb06a1f3d25" />
**Рисунок 4.** Связи таблиц маршрутов с публичными и приватными подсетями.

### Задание 4. Security Groups


<img width="1935" height="1012" alt="Снимок экрана 2026-10-14 231729" src="https://github.com/user-attachments/assets/829fedb3-61cc-407d-a5a6-6c61241255e4" />
**Рисунок 5.** Входящие правила `timecapsule-dev-bastion-sg`: SSH разрешён только с моего IP-адреса.

 
<img width="2178" height="1052" alt="Снимок экрана 2026-10-14 231836" src="https://github.com/user-attachments/assets/8e607c15-3166-4de1-9bd6-17e17521f1be" />
**Рисунок 6.** Входящие правила `timecapsule-dev-web-sg`: HTTP доступен из интернета, SSH разрешён согласно настройкам лабораторной работы.



<img width="2173" height="1015" alt="Снимок экрана 2026-10-14 231821" src="https://github.com/user-attachments/assets/5bc6fac5-2b36-4be8-ad52-8df52a2004bc" />
**Рисунок 7.** Входящие правила `timecapsule-dev-db-sg`: доступ к PostgreSQL разрешён от web-сервера, а не напрямую из интернета.

### Задание 5. Проверка публичного и приватного доступа


<img width="1238" height="498" alt="10" src="https://github.com/user-attachments/assets/176544fd-2e90-4094-a926-439a6034a45d" />
**Рисунок 8.** Страница web-сервера открывается в браузере по его публичному IP-адресу.


<img width="1020" height="482" alt="Снимок экрана 2026-10-14 235327" src="https://github.com/user-attachments/assets/d3e36086-e6f8-42b3-a69f-aa41aae37f93" />
**Рисунок 9.** Установка пакетов на db-сервер без NAT Gateway не завершается, поскольку у сервера нет выхода в интернет.

### Задание 6. Временный NAT Gateway


<img width="891" height="87" alt="Снимок экрана 2026-10-15 001538" src="https://github.com/user-attachments/assets/b947421b-ea0a-47e0-89da-956c328af6a0" />
**Рисунок 10.** Вывод `curl https://checkip.amazonaws.com` на db-сервер показывает внешний IP-адрес NAT Gateway.

<img width="1369" height="531" alt="Снимок экрана 2026-10-15 001834" src="https://github.com/user-attachments/assets/ec2441e2-930b-41ab-a126-7bef66cae08a" />
<img width="1104" height="352" alt="Снимок экрана 2026-10-16 153315" src="https://github.com/user-attachments/assets/7d5d7e18-a69a-4a50-997c-f1b9b35531bd" />
**Рисунок 11.** После удаления NAT Gateway маршрут `0.0.0.0/0` в приватной таблице маршрутов находится в состоянии `Blackhole`.


<img width="1385" height="336" alt="Снимок экрана 2026-10-15 001750" src="https://github.com/user-attachments/assets/feb0010f-9be9-42da-9c56-2caa6531c7fa" />
<img width="1104" height="352" alt="Снимок экрана 2026-10-16 153315" src="https://github.com/user-attachments/assets/4a015b6a-9fbc-4c1b-8b42-a361e568d7d9" />
**Рисунок 12.** NAT Gateway `timecapsule-dev-nat-a` находится в состоянии `Deleted`.

### Задание 7. Проверка подключения к базе данных и тегов


<img width="998" height="344" alt="Снимок экрана 2026-10-15 110043" src="https://github.com/user-attachments/assets/5fd9af60-244c-4c59-b4fd-5faa2097e56a" />
**Рисунок 13.** С web-сервера выполнен запрос к PostgreSQL, получена информация о версии сервера базы данных.


<img width="1089" height="126" alt="Снимок экрана 2026-10-15 110146" src="https://github.com/user-attachments/assets/baa757c5-1889-4bc7-b2af-8a2f3bd4b99c" />
**Рисунок 14.** Проверка порта `5432` с web-сервера подтверждает доступность PostgreSQL.


<img width="1093" height="145" alt="Снимок экрана 2026-10-15 110314" src="https://github.com/user-attachments/assets/0787e129-0745-4a8e-95e6-c1a6192fe94c" />
**Рисунок 15.** Проверка порта `5432` с bastion показывает результат доступа к базе данных из bastion.


<img width="1206" height="1056" alt="Снимок экрана 2026-10-15 112205" src="https://github.com/user-attachments/assets/6df9e9ff-dce4-4fa2-9bf4-28ba620d4053" />
<img width="939" height="64" alt="Снимок экрана 2026-10-15 112047" src="https://github.com/user-attachments/assets/3c62e40e-56fe-4d90-8d00-6357abf62f08" />
**Рисунок 16.** Результат эксперимента с Network ACL показывает влияние правил сетевого доступа на соединение.


<img width="655" height="957" alt="Снимок экрана 2026-10-15 113630" src="https://github.com/user-attachments/assets/fa98a9ea-2a32-4080-a1a2-0d806411e3d8" />
**Рисунок 17.** В Tag Editor отображаются ресурсы проекта с тегом `Project = timecapsule`.
