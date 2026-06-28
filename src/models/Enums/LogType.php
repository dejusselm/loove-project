<?php

enum LogType: string
{
    case ERROR = 'error';
    case ADMIN = 'admin';
    case ACTION = 'action';
    case INFO = 'info';

}