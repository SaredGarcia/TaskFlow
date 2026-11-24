<template>
  <q-layout view="hHh lpR fFf">
    <q-header class="topbar">
      <q-toolbar class="q-px-lg" style="min-height:72px">
        <div class="row items-center no-wrap q-gutter-sm">
          <div class="brand-mark"><q-icon name="task_alt" size="24px" /></div>
          <div>
            <div class="brand-name">TaskFlow</div>
            <div class="text-caption muted" style="font-size:10px;line-height:1">GESTOR DE ACTIVIDADES</div>
          </div>
        </div>
        <q-space />
        <div class="row items-center q-gutter-sm">
          <q-chip
            :color="apiConnected === true ? 'green-1' : apiConnected === false ? 'red-1' : 'grey-2'"
            :text-color="apiConnected === true ? 'positive' : apiConnected === false ? 'negative' : 'grey-7'"
            :icon="apiConnected === true ? 'cloud_done' : apiConnected === false ? 'cloud_off' : 'cloud_sync'"
            size="sm"
          >
            {{ apiConnected === true ? 'API conectada' : apiConnected === false ? 'API desconectada' : 'Comprobando API...' }}
          </q-chip>
          <q-avatar color="indigo-1" text-color="primary" size="36px">TF</q-avatar>
        </div>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <main class="page-shell">
        <section class="row items-end justify-between q-col-gutter-md q-mb-lg">
          <div class="col-12 col-md">
            <div class="eyebrow">Tu espacio de productividad</div>
            <h1 class="page-title">Mis actividades</h1>
            <div class="muted">Organiza tus pendientes, prioriza lo importante y avanza a tu ritmo.</div>
          </div>
          <div class="col-12 col-md-auto">
            <q-btn unelevated color="primary" icon="add" label="Nueva actividad" no-caps class="full-width"
              size="md" @click="openCreate" />
          </div>
        </section>

        <section class="row q-col-gutter-md q-mb-lg">
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="row items-center justify-between"><div class="stat-label">Total</div><div class="stat-icon"><q-icon name="view_list" /></div></div>
              <div class="stat-value">{{ stats.total }}</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="row items-center justify-between"><div class="stat-label">Pendientes</div><div class="stat-icon" style="background:#fff5e6;color:#ad6d10"><q-icon name="schedule" /></div></div>
              <div class="stat-value">{{ stats.pending }}</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="row items-center justify-between"><div class="stat-label">En progreso</div><div class="stat-icon"><q-icon name="autorenew" /></div></div>
              <div class="stat-value">{{ stats.in_progress }}</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="row items-center justify-between"><div class="stat-label">Completadas</div><div class="stat-icon" style="background:#e7f7f0;color:#16845e"><q-icon name="task_alt" /></div></div>
              <div class="stat-value">{{ stats.completed }}</div>
            </div>
          </div>
        </section>

        <section class="surface-card toolbar-card q-mb-md">
          <div class="row q-col-gutter-sm items-center">
            <div class="col-12 col-md">
              <q-input v-model="search" outlined dense clearable placeholder="Buscar por título..." @update:model-value="debouncedLoad">
                <template #prepend><q-icon name="search" color="grey-6" /></template>
              </q-input>
            </div>
            <div class="col-12 col-sm-5 col-md-3">
              <q-select v-model="statusFilter" outlined dense emit-value map-options :options="statusOptions"
                label="Filtrar por estado" clearable @update:model-value="loadActivities" />
            </div>
            <div class="col-12 col-sm-auto">
              <q-btn flat color="grey-7" icon="refresh" label="Actualizar" no-caps class="full-width" @click="loadActivities" />
            </div>
          </div>
        </section>

        <div v-if="errorMessage" class="q-mb-md">
          <q-banner rounded class="bg-red-1 text-negative">
            <template #avatar><q-icon name="error_outline" /></template>
            {{ errorMessage }}
            <template #action><q-btn flat color="negative" label="Reintentar" no-caps @click="loadActivities" /></template>
          </q-banner>
        </div>

        <div v-if="loading" class="surface-card q-pa-xl text-center">
          <q-spinner-dots color="primary" size="42px" />
          <div class="muted q-mt-sm">Cargando actividades...</div>
        </div>

        <section v-else-if="activities.length" class="row q-col-gutter-md">
          <div v-for="activity in activities" :key="activity.id" class="col-12 col-md-6">
            <article class="activity-card q-pa-md">
              <div class="row items-start no-wrap">
                <div class="col">
                  <div class="row items-center q-gutter-xs q-mb-sm">
                    <span :class="['pill', statusClass(activity.status)]">{{ statusLabel(activity.status) }}</span>
                    <span :class="['pill', priorityClass(activity.priority)]">{{ priorityLabel(activity.priority) }}</span>
                  </div>
                  <div class="activity-title">{{ activity.title }}</div>
                </div>
                <q-btn flat round dense icon="more_vert" color="grey-7">
                  <q-menu>
                    <q-list dense style="min-width:175px">
                      <q-item clickable v-close-popup @click="openEdit(activity)"><q-item-section avatar><q-icon name="edit" /></q-item-section><q-item-section>Editar</q-item-section></q-item>
                      <q-item clickable v-close-popup @click="cycleStatus(activity)"><q-item-section avatar><q-icon name="sync" /></q-item-section><q-item-section>Cambiar estado</q-item-section></q-item>
                      <q-separator />
                      <q-item clickable v-close-popup class="text-negative" @click="confirmDelete(activity)"><q-item-section avatar><q-icon name="delete_outline" color="negative" /></q-item-section><q-item-section>Eliminar</q-item-section></q-item>
                    </q-list>
                  </q-menu>
                </q-btn>
              </div>
              <p v-if="activity.description" class="activity-description q-mt-sm q-mb-md">{{ activity.description }}</p>
              <div class="row items-center justify-between q-mt-md">
                <div class="text-caption muted">
                  <q-icon name="event" class="q-mr-xs" />
                  {{ activity.due_date ? `Límite: ${formatDate(activity.due_date)}` : 'Sin fecha límite' }}
                </div>
                <div class="text-caption muted">Creada {{ formatDate(activity.created_at) }}</div>
              </div>
              <q-separator class="q-my-md" />
              <div class="row justify-end q-gutter-xs">
                <q-btn flat dense no-caps color="primary" label="Editar" icon="edit" @click="openEdit(activity)" />
                <q-btn flat dense no-caps color="grey-7" label="Estado" icon="sync" @click="cycleStatus(activity)" />
              </div>
            </article>
          </div>
        </section>

        <section v-else class="surface-card empty-state">
          <div class="empty-icon"><q-icon name="event_note" size="30px" /></div>
          <div class="text-h6 text-weight-bold text-dark">No hay actividades para mostrar</div>
          <div class="q-mt-xs">Crea una actividad o ajusta los filtros de búsqueda.</div>
          <q-btn class="q-mt-md" color="primary" unelevated no-caps icon="add" label="Crear actividad" @click="openCreate" />
        </section>

        <div v-if="lastPage > 1" class="row justify-center q-mt-lg">
          <q-pagination v-model="page" :max="lastPage" direction-links boundary-links color="primary" @update:model-value="loadActivities" />
        </div>

        <footer class="text-center text-caption muted q-pt-xl">© 2026 TaskFlow · Organiza tu día, una actividad a la vez.</footer>
      </main>
    </q-page-container>

    <q-dialog v-model="dialogOpen" persistent>
      <q-card style="width:560px;max-width:94vw;border-radius:18px">
        <q-card-section class="row items-center">
          <div>
            <div class="text-h6 text-weight-bold">{{ editing ? 'Editar actividad' : 'Nueva actividad' }}</div>
            <div class="text-caption muted">Completa los datos de tu actividad.</div>
          </div>
          <q-space />
          <q-btn flat round dense icon="close" v-close-popup />
        </q-card-section>
        <q-separator />
        <q-card-section>
          <q-form ref="formRef" @submit.prevent="saveActivity" class="q-gutter-md">
            <q-input v-model.trim="form.title" outlined label="Título *" maxlength="150"
              :rules="[v => !!v || 'El título es obligatorio', v => v.length <= 150 || 'Máximo 150 caracteres']" />
            <q-input v-model="form.description" outlined type="textarea" autogrow label="Descripción" maxlength="2000" />
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-sm-6">
                <q-select v-model="form.status" outlined emit-value map-options :options="statusOptions" label="Estado *"
                  :rules="[v => !!v || 'Selecciona un estado']" />
              </div>
              <div class="col-12 col-sm-6">
                <q-select v-model="form.priority" outlined emit-value map-options :options="priorityOptions" label="Prioridad *"
                  :rules="[v => !!v || 'Selecciona una prioridad']" />
              </div>
            </div>
            <q-input v-model="form.due_date" outlined type="date" label="Fecha límite" stack-label clearable />
            <div v-if="formError" class="text-negative text-caption">{{ formError }}</div>
            <div class="row justify-end q-gutter-sm">
              <q-btn flat no-caps label="Cancelar" color="grey-7" v-close-popup />
              <q-btn unelevated no-caps color="primary" :loading="saving" type="submit" :label="editing ? 'Guardar cambios' : 'Crear actividad'" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-layout>
</template>

<script setup>
import { onMounted, onUnmounted, reactive, ref } from 'vue'
import { Dialog, Notify } from 'quasar'
import api from './services/api'

const activities = ref([])
const stats = ref({ total: 0, pending: 0, in_progress: 0, completed: 0 })
const apiConnected = ref(null)
const loading = ref(false)
const saving = ref(false)
const errorMessage = ref('')
const formError = ref('')
const search = ref('')
const statusFilter = ref(null)
const page = ref(1)
const lastPage = ref(1)
const dialogOpen = ref(false)
const editing = ref(false)
const editingId = ref(null)
const formRef = ref(null)

const emptyForm = () => ({
  title: '',
  description: '',
  status: 'pending',
  priority: 'medium',
  due_date: null
})
const form = reactive(emptyForm())

const statusOptions = [
  { label: 'Pendiente', value: 'pending' },
  { label: 'En progreso', value: 'in_progress' },
  { label: 'Completada', value: 'completed' }
]
const priorityOptions = [
  { label: 'Baja', value: 'low' },
  { label: 'Media', value: 'medium' },
  { label: 'Alta', value: 'high' }
]

let searchTimer
function debouncedLoad () {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    page.value = 1
    loadActivities()
  }, 350)
}
onUnmounted(() => clearTimeout(searchTimer))

let latestRequestId = 0

async function loadActivities () {
  const requestId = ++latestRequestId
  loading.value = true
  errorMessage.value = ''

  try {
    const { data } = await api.get('/activities', {
      params: {
        search: search.value || undefined,
        status: statusFilter.value || undefined,
        page: page.value
      }
    })

    // Ignore older responses when a newer search or filter request has started.
    if (requestId !== latestRequestId) return

    activities.value = data.data || []
    lastPage.value = data.meta?.last_page || 1
    stats.value = {
      total: Number(data.meta?.stats?.total ?? 0),
      pending: Number(data.meta?.stats?.pending ?? 0),
      in_progress: Number(data.meta?.stats?.in_progress ?? 0),
      completed: Number(data.meta?.stats?.completed ?? 0)
    }
    apiConnected.value = true
  } catch (error) {
    if (requestId !== latestRequestId) return

    apiConnected.value = false
    errorMessage.value = error.response?.data?.message || 'No fue posible conectar con la API. Verifica que Laravel esté ejecutándose.'
    activities.value = []
    stats.value = { total: 0, pending: 0, in_progress: 0, completed: 0 }
  } finally {
    if (requestId === latestRequestId) {
      loading.value = false
    }
  }
}

function openCreate () {
  editing.value = false
  editingId.value = null
  Object.assign(form, emptyForm())
  formError.value = ''
  dialogOpen.value = true
}

function openEdit (activity) {
  editing.value = true
  editingId.value = activity.id
  Object.assign(form, {
    title: activity.title || '',
    description: activity.description || '',
    status: activity.status,
    priority: activity.priority,
    due_date: activity.due_date ? String(activity.due_date).slice(0, 10) : null
  })
  formError.value = ''
  dialogOpen.value = true
}

async function saveActivity () {
  const valid = await formRef.value?.validate()
  if (!valid) return
  saving.value = true
  formError.value = ''
  const payload = {
    ...form,
    description: form.description || null,
    due_date: form.due_date || null
  }
  try {
    if (editing.value) {
      await api.put(`/activities/${editingId.value}`, payload)
      Notify.create({ type: 'positive', message: 'Actividad actualizada correctamente.' })
    } else {
      await api.post('/activities', payload)
      Notify.create({ type: 'positive', message: 'Actividad creada correctamente.' })
      page.value = 1
    }
    dialogOpen.value = false
    await loadActivities()
  } catch (error) {
    const errors = error.response?.data?.errors
    formError.value = errors ? Object.values(errors).flat().join(' ') : (error.response?.data?.message || 'No se pudo guardar la actividad.')
  } finally {
    saving.value = false
  }
}

function confirmDelete (activity) {
  Dialog.create({
    title: 'Eliminar actividad',
    message: `¿Seguro que deseas eliminar "${activity.title}"? Esta acción no se puede deshacer.`,
    cancel: { flat: true, label: 'Cancelar', noCaps: true },
    ok: { color: 'negative', label: 'Eliminar', unelevated: true, noCaps: true },
    persistent: true
  }).onOk(async () => {
    try {
      await api.delete(`/activities/${activity.id}`)
      Notify.create({ type: 'positive', message: 'Actividad eliminada.' })
      if (activities.value.length === 1 && page.value > 1) page.value--
      await loadActivities()
    } catch {
      Notify.create({ type: 'negative', message: 'No fue posible eliminar la actividad.' })
    }
  })
}

async function cycleStatus (activity) {
  const order = ['pending', 'in_progress', 'completed']
  const next = order[(order.indexOf(activity.status) + 1) % order.length]
  try {
    await api.patch(`/activities/${activity.id}`, { status: next })
    Notify.create({ type: 'positive', message: `Estado cambiado a ${statusLabel(next)}.` })
    await loadActivities()
  } catch {
    Notify.create({ type: 'negative', message: 'No se pudo cambiar el estado.' })
  }
}

function statusLabel (value) {
  return ({ pending: 'Pendiente', in_progress: 'En progreso', completed: 'Completada' })[value] || value
}
function priorityLabel (value) {
  return ({ low: 'Prioridad baja', medium: 'Prioridad media', high: 'Prioridad alta' })[value] || value
}
function statusClass (value) {
  return ({ pending: 'pill-pending', in_progress: 'pill-progress', completed: 'pill-completed' })[value]
}
function priorityClass (value) {
  return ({ low: 'priority-low', medium: 'priority-medium', high: 'priority-high' })[value]
}
function formatDate (value) {
  if (!value) return '—'
  const date = new Date(`${String(value).slice(0, 10)}T00:00:00`)
  return new Intl.DateTimeFormat('es-MX', { day: '2-digit', month: 'short', year: 'numeric' }).format(date)
}

onMounted(loadActivities)
</script>
