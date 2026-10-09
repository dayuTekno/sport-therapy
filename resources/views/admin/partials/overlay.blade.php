<div
  @click="sidebarToggle = false"
  :class="sidebarToggle ? 'block lg:hidden' : 'hidden'"
  class="fixed inset-0 z-40 bg-gray-900/60 backdrop-blur-xs transition-opacity duration-300"
  aria-hidden="true"
></div>
