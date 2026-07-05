<?php
use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid {
    public function v4() {
        return RamseyUuid::uuid4()->toString();
    }
}
