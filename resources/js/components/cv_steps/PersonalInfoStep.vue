<template>
  <div class="form-step personal-info-step">
    <div class="step-intro">
      <h2>{{ t('steps.personalInfoDesc') }}</h2>
      <p>{{ t('cv.personalInfo') }}</p>
    </div>

    <div class="form-section">
      <div class="form-group">
        <label>{{ t('cv.title') }}</label>
        <input 
          v-model="cv.title" 
          type="text" 
          :placeholder="t('cv_title')"
          @input="$emit('input-change')"
          class="large-input"
        />
        <div class="form-hint">{{ t('cv.title') }}</div>
      </div>

      <div class="form-grid grid-2">
        <div class="form-group">
          <label>{{ t('personal.firstName') }} *</label>
          <input 
            v-model="cv.personalInfo.firstName" 
            type="text" 
            :placeholder="t('first_name')"
            @input="$emit('input-change')"
            autocomplete="given-name"
            name="firstName"
            required
          />
        </div>
        <div class="form-group">
          <label>{{ t('personal.lastName') }}</label>
          <input 
            v-model="cv.personalInfo.lastName" 
            type="text" 
            :placeholder="t('last_name')"
            @input="$emit('input-change')"
            autocomplete="family-name"
            name="lastName"
          />
        </div>
      </div>

      <div class="form-grid grid-2">
        <div class="form-group">
          <label>{{ t('personal.email') }} *</label>
          <input 
            v-model="cv.personalInfo.email" 
            type="email" 
            :placeholder="t('email')"
            @input="$emit('input-change')"
            autocomplete="email"
            name="email"
            required
          />
        </div>
        <div class="form-group">
          <label>{{ t('personal.phone') }}</label>
          <input 
            v-model="cv.personalInfo.phone" 
            type="tel" 
            :placeholder="t('phone')"
            @input="$emit('input-change')"
            autocomplete="tel"
            name="phone"
          />
        </div>
      </div>

      <div class="form-group">
        <label>{{ t('personal.address') }}</label>
        <input 
          v-model="cv.personalInfo.address" 
          type="text" 
          :placeholder="t('address')"
          @input="$emit('input-change')"
          autocomplete="street-address"
          name="address"
        />
      </div>

      <!-- Additional Personal Information Fields -->
      <div class="form-grid grid-2">
        <div class="form-group">
          <label>Date of Birth</label>
          <input 
            v-model="cv.personalInfo.dateOfBirth" 
            type="date" 
            @input="$emit('input-change')"
            name="dateOfBirth"
          />
        </div>
        <div class="form-group">
          <label>Place of Birth</label>
          <input 
            v-model="cv.personalInfo.placeOfBirth" 
            type="text" 
            placeholder="e.g., New York"
            @input="$emit('input-change')"
            name="placeOfBirth"
          />
        </div>
      </div>

      <div class="form-grid grid-2">
        <div class="form-group">
          <label>Gender</label>
          <select 
            v-model="cv.personalInfo.gender" 
            @change="$emit('input-change')"
            class="form-select"
            name="gender"
          >
            <option value="">Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
            <option value="prefer_not_to_say">Prefer not to say</option>
          </select>
        </div>
        <div class="form-group">
          <label>Nationality</label>
          <input 
            v-model="cv.personalInfo.nationality" 
            type="text" 
            placeholder="e.g., American"
            @input="$emit('input-change')"
            name="nationality"
          />
        </div>
      </div>

      <div class="form-grid grid-2">
        <div class="form-group">
          <label>Zip Code</label>
          <input 
            v-model="cv.personalInfo.zipCode" 
            type="text" 
            placeholder="e.g., 10001"
            @input="$emit('input-change')"
            name="zipCode"
          />
        </div>
        <div class="form-group">
          <label>Marital Status</label>
          <select 
            v-model="cv.personalInfo.maritalStatus" 
            @change="$emit('input-change')"
            class="form-select"
            name="maritalStatus"
          >
            <option value="">Select Status</option>
            <option value="single">Single</option>
            <option value="married">Married</option>
            <option value="divorced">Divorced</option>
            <option value="widowed">Widowed</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Driving License</label>
        <input 
          v-model="cv.personalInfo.drivingLicense" 
          type="text" 
          placeholder="e.g., B, C, D"
          @input="$emit('input-change')"
          name="drivingLicense"
        />
        <div class="form-hint">Enter your driving license categories (if any)</div>
      </div>

      <!-- Template Selection -->
      <div class="template-selection">
        <div class="template-header">
          <h3>🎨 {{ t('templates.selectTemplate') }}</h3>
          <p>{{ t('choose_cv_template') }}</p>
        </div>
        <div class="template-grid">
          <div 
            v-for="template in templates" 
            :key="template.id"
            @click="$emit('select-template', template.id)"
            class="template-card"
            :class="{ active: cv.selectedTemplate === template.id }"
          >
            <div class="template-preview">
              <div class="template-icon">
                <i :class="{
                  'fas fa-laptop-code': template.id === 'modern',
                  'fas fa-graduation-cap': template.id === 'classic', 
                  'fas fa-briefcase': template.id === 'professional',
                  'fas fa-palette': template.id === 'creative'
                }"></i>
              </div>
              <div class="template-check" v-if="cv.selectedTemplate === template.id">
                <i class="fas fa-check"></i>
              </div>
            </div>
            <div class="template-info">
              <h4>{{ template.name }}</h4>
              <p>{{ template.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

// Props
defineProps({
  cv: {
    type: Object,
    required: true
  },
  templates: {
    type: Array,
    required: true
  }
})

// Emits
defineEmits(['input-change', 'select-template'])
</script> 