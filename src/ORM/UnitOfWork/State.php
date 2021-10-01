<?php

namespace Electronics\Database\ORM\UnitOfWork;

enum State
{
    case ADDED;
    case MODIFIED;
    case DELETED;
    case PERSISTED;
}