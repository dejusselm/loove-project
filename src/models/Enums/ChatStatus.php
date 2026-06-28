<?php

enum ChatStatus: string
{
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case READ = 'read';
}