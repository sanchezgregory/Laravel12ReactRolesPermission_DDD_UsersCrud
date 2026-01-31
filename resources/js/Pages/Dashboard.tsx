import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import {
  Activity,
  ArrowDownRight,
  ArrowUpRight,
  CreditCard,
  DollarSign,
  Package,
  ShoppingCart,
  TrendingDown,
  TrendingUp,
  Users,
  AlertCircle
} from 'lucide-react';
import {
  Bar,
  BarChart,
  CartesianGrid,
  Cell,
  Pie,
  PieChart,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis
} from 'recharts';

interface KPI {
  title: string;
  value: string;
  icon: string;
  color: string;
  trend: string;
  change: string;
}

interface TopMediator {
  name: string;
  sales: number;
  revenue: number;
  trend: string;
}

interface IncomeDistribution {
  name: string;
  value: number;
  color: string;
}

interface Transaction {
  id: number;
  customer: string;
  email: string;
  mediator: string;
  amount: number;
  status: string;
  date: string;
}

interface DashboardProps {
  kpis: KPI[];
  topMediators: TopMediator[];
  incomeDistribution: IncomeDistribution[];
  recentTransactions: Transaction[];
  pendingConfirmation: Transaction[];
}

const iconMap: Record<string, any> = {
  DollarSign,
  Users,
  Activity,
  CreditCard,
  ShoppingCart,
  Package
};

const breadcrumbs: BreadcrumbItem[] = [];

export default function Dashboard({
  kpis = [],
  topMediators = [],
  incomeDistribution = [],
  recentTransactions = [],
  pendingConfirmation = []
}: DashboardProps) {

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Dashboard" />
      <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 overflow-x-auto">
        {/* KPIs Grid */}
        <div className="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-4">
          {kpis.map((kpi, index) => {
            const IconComponent = iconMap[kpi.icon] || Activity;
            return (
              <Card key={index} className="relative overflow-hidden border-0 bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 shadow-sm hover:shadow-md transition-all duration-300">
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium text-muted-foreground">
                    {kpi.title}
                  </CardTitle>
                  <IconComponent className={`h-4 w-4 ${kpi.color}`} />
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">{kpi.value}</div>
                  <div className="flex items-center text-xs text-muted-foreground">
                    {kpi.trend === 'up' && <TrendingUp className="mr-1 h-3 w-3 text-green-500" />}
                    {kpi.trend === 'down' && <TrendingDown className="mr-1 h-3 w-3 text-red-500" />}
                    <span className={kpi.trend === 'up' ? 'text-green-500' : kpi.trend === 'down' ? 'text-red-500' : ''}>
                      {kpi.change}
                    </span>
                  </div>
                </CardContent>
              </Card>
            );
          })}
        </div>

        {/* Charts Section */}
        <div className="grid gap-6 md:grid-cols-2">
          {/* Top 5 Mediators Chart */}
          <Card className="border-0 shadow-sm">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Users className="h-5 w-5" />
                Top 5 Mediadores (Sesiones Agendadas)
              </CardTitle>
              <CardDescription>
                Mediadores con mayor número de sesiones
              </CardDescription>
            </CardHeader>
            <CardContent>
              <ResponsiveContainer width="100%" height={300}>
                <BarChart data={topMediators}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="name" />
                  <YAxis />
                  <Tooltip />
                  <Bar dataKey="sales" fill="#8884d8" name="Sesiones" />
                </BarChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>

          {/* Income Distribution */}
          <Card className="border-0 shadow-sm">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <PieChart className="h-5 w-5" />
                Distribución de Ingresos (Categorías)
              </CardTitle>
            </CardHeader>
            <CardContent>
              <ResponsiveContainer width="100%" height={300}>
                <PieChart>
                  <Pie
                    data={incomeDistribution}
                    cx="50%"
                    cy="50%"
                    outerRadius={80}
                    fill="#8884d8"
                    dataKey="value"
                    label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
                  >
                    {incomeDistribution.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={entry.color} />
                    ))}
                  </Pie>
                  <Tooltip />
                </PieChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>
        </div>

        {/* Bottom Section */}
        <div className="grid gap-6 md:grid-cols-3">
          {/* Recent Transactions */}
          <Card className="md:col-span-2 border-0 shadow-sm">
            <CardHeader>
              <CardTitle className="flex items-center justify-between">
                <span className="flex items-center gap-2">
                  <CreditCard className="h-5 w-5" />
                  Transacciones Recientes
                </span>
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {recentTransactions.map((transaction) => (
                  <div key={transaction.id} className="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <div className="flex items-center gap-3">
                      <Avatar className="h-8 w-8">
                        <AvatarFallback>
                          {transaction.customer.split(' ').map(n => n[0]).join('').substring(0, 2)}
                        </AvatarFallback>
                      </Avatar>
                      <div>
                        <p className="font-medium">{transaction.customer}</p>
                        <p className="text-sm text-muted-foreground">{transaction.mediator}</p>
                      </div>
                    </div>
                    <div className="flex items-center gap-3">
                      <div className="text-right">
                        <p className="font-medium">${transaction.amount.toLocaleString()}</p>
                        <p className="text-sm text-muted-foreground">{transaction.date}</p>
                      </div>
                      <Badge
                        variant={
                          transaction.status === 'paid' || transaction.status === 'completed' ? 'default' :
                            transaction.status === 'pending' ? 'secondary' : 'destructive'
                        }
                      >
                        {transaction.status}
                      </Badge>
                    </div>
                  </div>
                ))}
                {recentTransactions.length === 0 && (
                  <div className="text-center text-muted-foreground py-4">No hay transacciones recientes</div>
                )}
              </div>
            </CardContent>
          </Card>

          {/* Pending Confirmations Alert */}
          <Card className="border-0 shadow-sm border-l-4 border-l-red-500">
            <CardHeader>
              <CardTitle className="flex items-center gap-2 text-red-600">
                <AlertCircle className="h-5 w-5" />
                Confirmación Pendiente
              </CardTitle>
              <CardDescription>
                Transacciones pagadas no confirmadas
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {pendingConfirmation.map((transaction, index) => (
                  <div key={index} className="flex items-center justify-between p-3 rounded-lg bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/20">
                    <div className="flex-1">
                      <div className="flex items-center gap-2">
                        <p className="font-medium text-sm">{transaction.customer}</p>
                      </div>
                      <p className="text-xs text-muted-foreground">Mediator: {transaction.mediator}</p>
                      <p className="text-xs text-muted-foreground">{transaction.date}</p>
                    </div>
                    <div className="text-right ml-2">
                      <p className="font-medium text-red-600">${transaction.amount.toLocaleString()}</p>
                    </div>
                  </div>
                ))}
                {pendingConfirmation.length === 0 && (
                  <div className="flex flex-col items-center justify-center py-8 text-muted-foreground">
                    <Activity className="h-8 w-8 mb-2 opacity-20" />
                    <p>Todo en orden</p>
                  </div>
                )}
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </AppLayout>
  );
}