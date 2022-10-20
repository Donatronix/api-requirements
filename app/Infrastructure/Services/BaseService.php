<?php

    declare(strict_types=1);

    namespace App\Domain\Services;

    use App\Domain\Services\Interfaces\BaseServiceInterface;
    use Exception;
    use Illuminate\Support\Facades\DB;
    use Prettus\Validator\Contracts\ValidatorInterface;
    use RuntimeException;
    use Throwable;

    class BaseService implements BaseServiceInterface
    {
        /**
         * @param array $request
         *
         * @throws Exception|Throwable
         */
        public function store(array $request): mixed
        {
            try {
                DB::beginTransaction();
                $this->getValidator()->with($request)->passesOrFail(ValidatorInterface::RULE_CREATE);
                $value = $this->getRepository()->create($request);
                DB::commit();

                return $value;
            } catch (Throwable $e) {
                DB::rollBack();
                throw new RuntimeException($e->getMessage());
            }
        }

        /**
         * @throws Exception
         */
        public function getValidator(): mixed
        {
            throw new RuntimeException('The service should implement `getValidator` method');
        }

        /**
         * @throws Exception
         */
        public function getRepository(): mixed
        {
            throw new RuntimeException('The service should implement `getRepository` method');
        }

        /**
         * @param array $request
         * @param       $id
         *
         * @return mixed
         *
         * @throws Exception|Throwable
         */
        public function update(array $request, $id): mixed
        {
            try {
                DB::beginTransaction();
                $this->getValidator()->with($request)->passesOrFail(ValidatorInterface::RULE_UPDATE);
                $this->getRepository()->update($request, $id);
                DB::commit();

                return $this->getRepository()->find($id);
            } catch (Throwable $th) {
                throw new RuntimeException($th->getMessage());
            }
        }

        /**
         * @param $id
         *
         * @return int|null
         *
         * @throws Throwable
         */
        public function delete($id): ?int
        {
            try {
                return $this->getRepository()->delete($id);
            } catch (Throwable $th) {
                throw new RuntimeException($th->getMessage());
            }
        }
    }
