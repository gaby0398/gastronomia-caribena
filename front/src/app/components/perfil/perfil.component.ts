import { Component, OnInit, inject } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ManagerUserService } from '../../shared/services/manager-user.service';
import { AuthService } from '../../shared/services/auth.service';
import Swal from 'sweetalert2';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-perfil',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './perfil.component.html',
  styleUrls: ['./perfil.component.css']
})
export class PerfilComponent implements OnInit {
  private srvAuth = inject(AuthService);
  private managerUserService = inject(ManagerUserService);

  perfilForm: FormGroup;
  loading: boolean = false;
  fotoPerfilUrl: string = 'assets/default-profile.png'; // Foto inicial por defecto

  constructor(private fb: FormBuilder) {
    this.perfilForm = this.fb.group({
      nombre: ['', [Validators.required]],
      apellido1: ['', [Validators.required]],
      apellido2: ['', [Validators.required]],
      usuario: [{ value: '', disabled: true }, [Validators.required]],
      correo: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.minLength(6)]],
      confirmPassword: ['', [Validators.minLength(6)]],
      genero: ['', [Validators.required]]
    }, { validators: this.passwordMatchValidator });
  }

  ngOnInit(): void {
    const usuarioActual = this.srvAuth.valorUsrActual;

    if (usuarioActual) {
      this.managerUserService.getUser(usuarioActual.alias).subscribe({
        next: (userData) => {
          this.perfilForm.patchValue(userData);
          this.fotoPerfilUrl = userData.foto || 'assets/default-profile.png';
        },
        error: () => Swal.fire('Error', 'No se pudo cargar el perfil.', 'error')
      });
    }
  }

  triggerFileUpload(): void {
    const fileInput = document.getElementById('file-upload') as HTMLInputElement;
    fileInput.click();
  }

  onFileSelected(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) {
      const formData = new FormData();
      formData.append('foto', file);

      this.managerUserService.uploadProfilePhoto(formData).subscribe({
        next: (response) => {
          this.fotoPerfilUrl = response.url;
          Swal.fire('Éxito', 'Foto actualizada correctamente', 'success');
        },
        error: () => Swal.fire('Error', 'Error al subir la foto.', 'error')
      });
    }
  }

  passwordMatchValidator(form: FormGroup): null {
    const password = form.get('password')?.value;
    const confirmPassword = form.get('confirmPassword')?.value;

    if (password !== confirmPassword) {
      form.get('confirmPassword')?.setErrors({ mismatch: true });
    } else {
      form.get('confirmPassword')?.setErrors(null);
    }
    return null;
  }

  actualizarPerfil() {
    if (this.perfilForm.invalid) {
      Swal.fire('Error', 'Corrige los errores del formulario.', 'error');
      return;
    }
  
    this.loading = true;
  
    const alias = this.srvAuth.valorUsrActual.alias; // Primer argumento
    const datosActualizacion = this.perfilForm.value; // Segundo argumento
  
    this.managerUserService.updateUser(alias, datosActualizacion).subscribe({
      next: () => {
        Swal.fire('Éxito', 'Perfil actualizado correctamente.', 'success');
      },
      error: () => {
        Swal.fire('Error', 'No se pudo actualizar el perfil.', 'error');
      },
      complete: () => (this.loading = false)
    });
  }
  
}
