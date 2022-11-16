<div x-data="{
    show: false,
    type: 'default',
    message: ''
  }
">
  <div
    @alert.window="
      show = true;
      type = $event.detail.type;
      message = $event.detail.message;
      $el.classList.add(...(() => {
        if (type === 'success') {
          return ['bg-green-500', 'text-green-200', 'border-green-200'];
        }

        if (type === 'error') {
          return ['bg-green-500', 'text-green-200', 'border-green-200'];
        }

        if (type === 'warning') {
          return ['bg-yellow-500', 'text-yellow-200', 'border-yellow-200'];
        }

        return ['bg-blue-500', 'text-blue-200', 'dark:bg-indigo-500', 'dark:text-indigo-200', 'border-blue-200', 'dark:border-indigo-600'];
      })());
      setTimeout(() => { show = false }, 3600);
    "
    @click.outside="show = false"
    x-show="show"
    class="grid justify-items-start content-center grid-cols-3 gap-4 absolute top-0 right-0 max-w-lg p-3 mr-16 mt-16 rounded-lg shadow-lg border z-[500]"
  >
    <div class="col-span-2 text-base font-semibold">
      <div class="inline-flex items-center justify-start">
        <i class="mr-2 fa-solid fa-circle-info fa-fw"></i> <div class="text-base font-semibold" x-text="message"></div>
      </div>
    </div>
    <button @click="show = false" class="ml-auto text-sm border-transparent rounded-full hover:opacity-75 hover:shadow-inner">
      <i class="fa-solid fa-xmark fa-fw"></i>
    </button>
  </div>
</div>
