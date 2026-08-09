import { BrowserRouter, Navigate, Route, Routes } from 'react-router-dom'
import { AuthProvider } from '@/lib/auth'
import { ProtectedRoute } from '@/components/ProtectedRoute'
import { AppShell } from '@/components/AppShell'
import { LoginPage } from '@/features/auth/LoginPage'
import { DashboardPage } from '@/features/dashboard/DashboardPage'
import { TicketsPage } from '@/features/tickets/TicketsPage'
import { TicketDetailPage } from '@/features/tickets/TicketDetailPage'
import { CreateTicketPage } from '@/features/tickets/CreateTicketPage'
import { NotificationsPage } from '@/features/notifications/NotificationsPage'
import { ReportsPage } from '@/features/reports/ReportsPage'
import { ProfilePage } from '@/features/profile/ProfilePage'

export default function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <Routes>
          <Route path="/login" element={<LoginPage />} />

          <Route element={<ProtectedRoute><AppShell /></ProtectedRoute>}>
            <Route index element={<DashboardPage />} />
            <Route path="tickets" element={<TicketsPage />} />
            <Route path="my-tickets" element={<TicketsPage />} />
            <Route path="tickets/new" element={<CreateTicketPage />} />
            <Route path="tickets/:id" element={<TicketDetailPage />} />
            <Route path="notifications" element={<NotificationsPage />} />
            <Route
              path="reports"
              element={<ProtectedRoute roles={['admin', 'it_support']}><ReportsPage /></ProtectedRoute>}
            />
            <Route path="profile" element={<ProfilePage />} />
          </Route>

          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </AuthProvider>
    </BrowserRouter>
  )
}
